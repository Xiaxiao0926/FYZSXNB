# FYZSXNB TAKEOVER — Deployment Hardening Gate 001

**Task ID:** `FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-001`
**性质:** LOCAL DEPLOYMENT TOOL HARDENING / FAIL-CLOSED（零生产写入、零 git 写）
**前置 Gate:** Forensics / Baseline / Git Cleanup / CURRENT_TASK / Feed Parity = FINAL PASS
**执行人:** DeepSeek | **日期:** 2026-08-27（执行基线 2026-08-26）

---

## Executive Summary

两个部署入口（`ftp_p0_deployer.ps1`、`run_ftp_deploy_secure.ps1`）已从"缺参可能默认覆盖 mu-plugin"改为 **fail-closed**：RemotePath/LocalPath 必填（危险默认值彻底移除）、默认=只读 PREVIEW、写路径需 `-Execute` + `-ConfirmProductionWrite <exact remote>` 双重授权、更新需 `-ExpectedRemoteSha256` 前置匹配、CREATE 需显式 `-AllowCreate`、同哈希=NO_CHANGE、更新前强制备份（失败中止）、上传后重新下载做字节 SHA256+size 验证（失败非零退出）、plan/result 分离、凭证零输出。测试矩阵 **25/25 PASS**（T01-T18 + 副断言，子进程退出码 + mock 传输）；真实生产只读 preflight 三文件哈希与 `PROD-BASELINE-20260826-R1` **零漂移**。另发现并登记 3 个历史 Python FTP 写入口（未在本 Gate 硬化，P1 观察）。`DEPLOYMENT_HARDENING_GATE = PASS`，无任何生产写入。

## 1. Original Deployment Risk

- `ftp_p0_deployer.ps1` L9 与 `run_ftp_deploy_secure.ps1` L9 均有 `$RemotePath = 'wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php'` 默认值；`deploy` 路径虽有上传后哈希校验，但缺默认不写、前置条件、确认、备份、同哈希跳过。→ `DEPLOYMENT_SCRIPT_HAZARD = HIGH`。

## 2. Original Script Call Graph

```
run_ftp_deploy_secure.ps1 (wrapper)
  └─ 读加密凭据 clixml → env FYZSXNB_FTP_* → 调用
     ftp_p0_deployer.ps1 (执行体)
        ├─ snapshot  : LIST + RETR + 本地备份（只读+写本地）
        ├─ deploy    : local read → Ensure dir → STOR → RETR verify（原已有；无fail-closed）
        ├─ verify    : RETR + hash（只读）
        └─ rollback  : 读快照 → STOR/RETR（写路径，原无门禁）
```

## 3. Production Write Entrypoints

| 入口 | 类型 | 目标 | 状态 |
|---|---|---|---|
| `ftp_p0_deployer.ps1` | FTP STOR | 显式/默认 mu-plugin（旧） | **本 Gate 硬化** |
| `run_ftp_deploy_secure.ps1` | wrapper | 同下 | **本 Gate 硬化** |
| `deploy_frontend_patch.py` | FTP storbinary | FILES_TO_DEPLOY 显式列表 | P1 观察（无确认/备份/verify 闭环） |
| `deploy_home_inc.py` | FTP storbinary | FILES_TO_DEPLOY 显式列表 | P1 观察（同上） |
| `deploy_kuajing_plugin.py` | FTP storbinary | 硬编码 kuajing-persistence.php | P1 观察（同上；目标非 mu-plugin） |

其余 `upload_media_asset/upload_with_retry` 系 WordPress REST 媒体上传（非 FTP 部署），不计入。

## 4. Pre-Hardening Hashes

- `work/tmp/deployment-hardening-pre-20260827/`：`ftp_p0_deployer.ps1`（8,518B, `177199F6…`）、`run_ftp_deploy_secure.ps1`（1,771B, `B58772EE…`），`pre-hardening-manifest.json` 记录，字节级保留。

## 5. Hardened Deployment Contract

- 完整文档：`work/site-ops/DEPLOYMENT-CONTRACT.md`（十条规则 + 执行要点 + 入口清单）。

## 6. Parameter Safety

- RemotePath：`[Parameter(Mandatory=$true)]` + 运行时校验（空/空白/`..`/`*`/`?`/控制字符/尾斜杠拒绝，`\`→`/` 规范化）；wrapper 同样拒绝空值（**无任何默认 target 残留**，全文件搜索确认）。
- LocalPath：deploy 必填，校验存在/文件/非空。
- 无"默认最近文件"、无递归镜像模式。

## 7. Fail-Closed Behavior

- 默认（无 `-Execute`）→ 完整 PREFLIGHT（只读）+ PLAN 输出（含 "DRY RUN / PREVIEW ONLY / NO PRODUCTION WRITE OCCURRED" 头）+ plan JSON；exit 0。写代码全部位于 preflight 之后。
- `-Execute` 缺 `-ConfirmProductionWrite`（token 必须 == RemotePath 精确值）→ exit 2 BLOCK。
- rollback 同样要求 `-Execute` + 确认。

## 8. Remote Precondition Design

- UPDATE：`-ExpectedRemoteSha256` 必填且必须 == 远端当前实际 SHA256，否则 exit 3（`BLOCKED_REMOTE_PRECONDITION` plan 记录）。
- CREATE：远端缺失 + 无 `-AllowCreate` → exit 3（`BLOCKED_NO_ALLOW_CREATE`）；创建不会由 UPDATE 自动变体。

## 9. Backup Design

- UPDATE 执行前：下载当前远端 → `work/deployments/backups/<timestamp>/<basename>`；备份失败（含 mock hook 注入）→ exit 4，**中止上传**，远端不变（T15 验证）。CREATE 时 `backup_path=null` + `predeploy_remote_exists=false` 记录。

## 10. Post-Deploy Verification

- 上传后重新下载远端 → 字节 SHA256 + size 双校验；不匹配 → exit 6（T16 验证）；仅远端字节 == 本地时才输出 `status=DEPLOYED, verification=PASS` 并写 result JSON（T17/T17b 验证 mock 内容一致）。
- `declared success ≠ production evidence`：PREVIEW 不产生 result 文件；result 仅在真实写入+字节验证后生成。

## 11. Credential / Logging Audit

- 密码仅用于 `NetworkCredential`（FTP）与进程 env；plan/result JSON、控制台、错误消息均不含凭据；T18 扫描 0 泄漏。wrapper finally 清理 env 并置空密码对象。

## 12. Test Matrix

- `work/site-ops/tests/run_deployment_hardening_tests.ps1`（T01-T18 + 副断言）→ `work/qa/FYZSXNB-DEPLOYMENT-HARDENING-001/test-results.json/.md`。
- **25/25 PASS**：T01-T07 参数失败（exit 1）、T08/T10/T14 preview 无写、T09 exit 2、T11/T13 exit 3、T12 NO_CHANGE、T15 exit 4、T16 exit 6、T17/T17b 正向 DEPLOYED（UPDATE/CREATE，mock 远端字节==本地）、T18 0 泄漏；全部 mock 远端"未变/已变"断言通过。
- 子进程模式（`pwsh -File`）保证退出码权威。

## 13. Real Production Read-Only Preflight

- 硬化 wrapper `verify`（只读）实测：feed 插件 `4997b969…` == 基线；style.css `281199d4…` == 基线；home.php `d15f32fb…` == 基线 → `REAL_PRODUCTION_PREFLIGHT = PASS`。

## 14. Production Drift Check

- `PRODUCTION_DRIFT_DETECTED = NO`（三文件与 `PROD-BASELINE-20260826-R1` 一致；mu-plugin 基线 8aa9aa8a 前 Gate 已验）。

## 15. Git Diff Scope

- git 前后一致（133 = 1 modified L1 + 132 untracked D1；site-ops 在仓库外）；无主题/feed/mu-plugin 新增 diff；CURRENT_TASK、feed baseline 未动。备份/测试/契约/报告均落在预期目录。

## 16. Remaining Governance Debt

- 3 个历史 Python FTP 部署器（`deploy_frontend_patch.py`/`deploy_home_inc.py`/`deploy_kuajing_plugin.py`）未硬化（P1 观察；建议后续迁移到本契约工具或加同等门禁）。
- `core.autocrlf` 换行治理未处理（映射至 Governance State Commit 后的独立项）。
- Automotive ru-auto taxonomy 债务（1065/1077/1084/1093/1098，P2）未动；内容发布保持暂停；Resolver V2 / translation-pairs 未部署。

## 17. Recommended Next Action

`FYZSXNB-TAKEOVER-GOVERNANCE-STATE-COMMIT-001`（统一提交：CURRENT_TASK 重写版、Feed Parity Baseline R1、治理五个 Gate 报告、Hardening 脚本/测试/契约；继续排除 V2 与无价值临时产物）。

## 18. STOP Declaration

本 Gate 未执行任何 FTP upload/delete/rename；未发布内容；未改 CURRENT_TASK/feed baseline/mu-plugin/feed plugin/theme/translation-pairs；未 git 写操作。唯一落地产出：硬化脚本 ×2 + 测试 harness + DEPLOYMENT-CONTRACT.md + 备份 + test-results + 本报告。

---

## §57 终值

```text
DEPLOYMENT_HARDENING_GATE = PASS

TRUSTED_PRODUCTION_BASELINE = PROD-BASELINE-20260826-R1
TRUSTED_GIT_BASELINE = a4c87bc
TRUSTED_FEED_BASELINE = FEED-BASELINE-20260826-R1

HARDENED_SCRIPT_COUNT = 2（ftp_p0_deployer.ps1 + run_ftp_deploy_secure.ps1）
PRODUCTION_WRITE_ENTRYPOINT_COUNT = 5（2 硬化 + 3 Python P1 观察）

DEFAULT_REMOTE_TARGET_REMOVED = YES
DEFAULT_LOCAL_TARGET_REMOVED = YES
DEFAULT_MODE = PREVIEW_ONLY

EXPLICIT_EXECUTE_REQUIRED = YES
EXPLICIT_PRODUCTION_CONFIRMATION_REQUIRED = YES（token == exact remote path）
REMOTE_PATH_VALIDATION = PASS
REMOTE_PRECONDITION_REQUIRED_FOR_UPDATE = YES
ALLOW_CREATE_EXPLICIT = YES
SAME_HASH_UPLOAD_BLOCKED = YES
BACKUP_BEFORE_UPDATE_REQUIRED = YES
BACKUP_FAILURE_BLOCKS_UPLOAD = YES
POST_UPLOAD_REMOTE_SHA_VERIFY = YES
POST_UPLOAD_HASH_MISMATCH_RETURNS_FAILURE = YES
FILE_SHA256_USED_FOR_DEPLOYMENT = YES
GIT_BLOB_HASH_USED_AS_DEPLOYMENT_HASH = NO
SECRET_LEAK_COUNT = 0

NEGATIVE_TESTS = PASS
MOCK_POSITIVE_TESTS = PASS
REAL_PRODUCTION_PREFLIGHT = PASS
PRODUCTION_DRIFT_DETECTED = NO

PRODUCTION_WRITE_OCCURRED = NO
FTP_UPLOAD_OCCURRED = NO
CURRENT_TASK_MODIFIED = NO
FEED_BASELINE_MODIFIED = NO
BUSINESS_APPLICATION_CODE_MODIFIED = NO
GIT_COMMIT_OCCURRED = NO
GIT_PUSH_OCCURRED = NO

DEPLOYMENT_CONTRACT_PATH = work/site-ops/DEPLOYMENT-CONTRACT.md
TEST_RESULTS_PATH = work/qa/FYZSXNB-DEPLOYMENT-HARDENING-001/test-results.json
REPORT_PATH = work/agent-handoff/FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-001.md

READY_FOR_GOVERNANCE_STATE_COMMIT = YES
RECOMMENDED_NEXT_ACTION = FYZSXNB-TAKEOVER-GOVERNANCE-STATE-COMMIT-001
```

---

## §60 STOP Gate 遵守声明

已完成：脚本硬化、测试矩阵、只读生产 preflight、部署契约、报告。未执行 git add/commit/push、任何真实上传、CURRENT_TASK 重写、内容发布、分类修复、换行治理、Resolver V2/translation-pairs 部署、性能工作。生产零写入。

`GATE_COMPLETE` — 等待审核。