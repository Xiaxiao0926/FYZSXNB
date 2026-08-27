# FYZSXNB TAKEOVER — Deployment Hardening Python Entrypoints Closure 001

**Task ID:** `FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-PYTHON-ENTRYPOINTS-001`
**性质:** LOCAL LEGACY WRITE-SURFACE CLOSURE / FAIL-CLOSED（零生产写入、零 git 写）
**前置:** PowerShell Hardening = PASS（25/25）→ 本 Gate 关闭遗留 Python 写面
**执行人:** DeepSeek | **日期:** 2026-08-27

---

## Executive Summary

全工作区扫描确认直接 FTP 写实现仅存于 3 个遗留 Python 部署器（`deploy_frontend_patch.py`、`deploy_home_inc.py`、`deploy_kuajing_plugin.py`，合计 4 处 `storbinary`）；另发现 `add_ftp_retry.py`（会重建遗留部署器的修改工具），一并处理。4 个脚本全部重写为 **tombstone（fail-closed）**：无任何 ftplib/STOR/凭据代码（`DIRECT_STOR_CODE_REMAINING = 0`），直接运行一律打印废弃指引并以 exit 1 退出。两个 legacy wrapper（`run_deploy_home.ps1`/`run_deploy_kuajing.ps1`）补上缺失的退出码传播（原 pwsh -File 无 `exit` 语句恒返 0——真实缺陷，已修）。测试矩阵 **PY01-PY15 全 PASS（14/14 项）**；修改后全工作区写面重扫 = 0 写原语残留；生产只读 drift 检查与 `PROD-BASELINE-20260826-R1` 零漂移。`PYTHON_ENTRYPOINT_HARDENING_GATE = PASS`，生产写实现收敛为 **1 个**（PowerShell 硬化链）。备份在 `work/tmp/deployment-hardening-python-pre-20260827/`（含 manifest）。

## 1. Legacy Python Entrypoints Discovered

| # | 路径 | 行数 | 原写点 |
|---|---|---|---|
| 1 | `work/site-ops/deploy_frontend_patch.py` | 59 | L29 `ftp.storbinary`（3 个主题文件批量） |
| 2 | `work/site-ops/deploy_home_inc.py` | 72 | L43 `ftp.storbinary`（6 个主题模板文件） |
| 3 | `work/site-ops/deploy_kuajing_plugin.py` | 81 | L37/L60 `ftp.storbinary`（kuajing-persistence 插件 + dist **递归目录**上传，含 `mkd`/`cwd`） |
| 4 | `project-wide` | — | `add_ftp_retry.py`（重写 deploy_home_inc.py 的工具，防重建一并 tombstone） |

扫描范围：`*.py/ps1/cmd/bat/txt/bak/old`，`ORIGINAL_LEGACY_PYTHON_WRITE_ENTRYPOINT_COUNT = 3`（add_ftp_retry 为辅助，计入共 4 个 tombstone）。

## 2. Call-Site Audit

- `run_deploy_home.ps1` → 调 deploy_home_inc.py（env 注入包装）
- `run_deploy_kuajing.ps1` → 调 deploy_kuajing_plugin.py（FTP+WP 凭据注入包装）
- deploy_frontend_patch.py：无包装调用方（文档级引用）；add_ftp_retry.py：历史修改器（曾于 8-21 给 home_inc 加 retry）
- 分类：frontend_patch/home_inc = **LEGACY_UNUSED**（一次性补丁部署，部署已完成且生产=本地已验证）；kuajing = **UNCERTAIN**（D:\ozon 独立项目仍可能迭代发布新版本）→ 依 §6 不删除，采用 tombstone + 指引（kuajing 未来发布需专项批准 gate）；add_ftp_retry = **LEGACY_UNUSED**（防重建）。

## 3. Direct FTP Write Audit

- 修改前：4 处 storbinary（frontend_patch:29、home_inc:43、kuajing:37/60）。
- 修改后：**0**（tombstone 无任何网络写原语；静态断言 PY06 + 全工作区重扫 PY07 双证）。

## 4. Credential Audit

- 原脚本均读 env（`FYZSXNB_FTP_*` / `WP_*`），无明文硬编码 → `PLAINTEXT_SECRET_DETECTED = NO`。
- tombstone 不含任何凭据读取/引用；T/PY14 扫描 0 泄漏。

## 5. Active vs Legacy Classification

| 脚本 | 现役价值 | 处置 |
|---|---|---|
| deploy_frontend_patch.py | LEGACY_UNUSED | DEPRECATED（tombstone） |
| deploy_home_inc.py | LEGACY_UNUSED | DEPRECATED（tombstone） |
| deploy_kuajing_plugin.py | UNCERTAIN（kuajing 项目） | DEPRECATED（tombstone）+ 专项 gate 指引 |
| add_ftp_retry.py | LEGACY_UNUSED | DEPRECATED（tombstone，防重建） |

## 6. Final Treatment Per Entrypoint

全部 **DEPRECATED + FAIL CLOSED**（不留可执行副本、不保留隐藏开关；直接运行 exit 1 + 明确指向 `run_ftp_deploy_secure.ps1` / `DEPLOYMENT-CONTRACT.md`）。

## 7. Wrapper / Deprecation Design

- 未引入 Python→hardened 路由（保持 ONE PRODUCTION WRITE IMPLEMENTATION）；legacy wrapper 经 tombstone 自然失败。
- **真实缺陷修复**：`run_deploy_home.ps1`/`run_deploy_kuajing.ps1` 原无 `exit $LASTEXITCODE`，pwsh -File 模式恒返 0（吞掉失败）——已补（PY15 修复验证 exit=1 传播）。

## 8. Fail-Closed Verification

静态（无写原语）+ 动态（4 tombstone 直接运行 exit 1、无参数/带参一律 exit 1、wrapper 链 exit 1）双证；无任何"warning→继续"路径。

## 9. Test Matrix

`work/qa/FYZSXNB-DEPLOYMENT-HARDENING-PYTHON-ENTRYPOINTS-001/run_python_entrypoint_tests.ps1` → **14/14 PASS**（PY01×4 tombstone、PY02 无参、PY05 无默认目标、PY06 无 FTP 原语、PY07 写面清零、PY07b 只读检查器无写原语、PY08/PY09/PY15 wrapper 失败传播、PY13 PowerShell 矩阵复验 25/25、PY14 0 泄漏）。artifact：`test-results.json/.md`、`write-surface-matrix.json/.md`。

## 10. Post-Change Write-Surface Scan

全工作区（排除 node_modules/tmp/deployments/git）：`storbinary|\.stor\(` 命中 **0**。`DIRECT_WRITE_SEARCH_RESULTS_AFTER = 0`。仅存写实现 = PowerShell 硬化链（FtpWebRequest，非本扫描模式）。

## 11. Production Read-Only Drift Check

- mu-plugin `8aa9aa8af1da…` == PROD-BASELINE ✓；theme functions.php `3acf887a…` == 基线 ✓；feed 插件 `4997b969…`（上一 Gate 硬化 wrapper 验证）== 基线 ✓。
- `PRODUCTION_DRIFT_DETECTED = NO`；全程仅 make FTP verify（只读）。

## 12. Git Diff Scope

- site-ops/ 位于仓库外；fyzsxnb-ui-v2 git 无新变化（133 = 1 L1 modified + 132 D1 untracked，与本 Gate 无关）。无主题/feed/mu-plugin/content/CURRENT_TASK 改动。

## 13. Remaining Deployment Governance Debt

- kuajing-persistence 插件未来发布流程未定义（需专项批准 gate，按 hardened contract 逐文件部署）。
- 只读检查器（check_ftp_root 等 7 个）保留（无写能力，审计用途）。
- `core.autocrlf` 换行治理未处理（既有项）；Automotive ru-auto taxonomy（P2）、Resolver V2、translation-pairs 部署保持原状态。

## 14. Final Deployment Hardening Status

```text
SYSTEM_WIDE_PRODUCTION_WRITE_SURFACE_HARDENED = YES
PRODUCTION_WRITE_IMPLEMENTATION_COUNT = 1（PowerShell 硬化链）
unhardened known entrypoints = 0
→ OVERALL_DEPLOYMENT_HARDENING_READY_FOR_FINAL_PASS = YES
```

## 15. Recommended Next Action

`FYZSXNB-TAKEOVER-GOVERNANCE-STATE-COMMIT-001`（统一提交治理状态）。

## 16. STOP Declaration

本 Gate 仅修改：4 个 Python tombstone、2 个 legacy wrapper（补 exit 传播）、测试 harness 与 QA artifacts、本报告；备份在 `work/tmp/deployment-hardening-python-pre-20260827/`（4 文件 + manifest，SHA 记录于报告 §0 外清单）。未执行任何生产写入/上传/删除/改名、未 git 写、未改 CURRENT_TASK/feed baseline/业务代码、未开始内容发布。

---

## §36 终值

```text
PYTHON_ENTRYPOINT_HARDENING_GATE = PASS

ORIGINAL_LEGACY_PYTHON_WRITE_ENTRYPOINT_COUNT = 3
FINAL_DIRECT_LEGACY_PYTHON_WRITE_ENTRYPOINT_COUNT = 0

PYTHON_ENTRYPOINTS_AUDITED = 4（3 写入口 + add_ftp_retry）
PYTHON_ENTRYPOINTS_DEPRECATED = 4
PYTHON_ENTRYPOINTS_ROUTED_TO_HARDENED_PATH = 0（保持 ONE IMPLEMENTATION）
UNKNOWN_PYTHON_ENTRYPOINTS = 0

DIRECT_STOR_CODE_REMAINING = 0

ALL_KNOWN_PRODUCTION_WRITE_ENTRYPOINTS = 4（2 PS 硬化 + 1 PS wrapper + 0 Python；另 kuajing/others 观察项已在矩阵列明）
UNHARDENED_KNOWN_PRODUCTION_WRITE_ENTRYPOINTS = 0
PRODUCTION_WRITE_IMPLEMENTATION_COUNT = 1

NO_ARGUMENT_WRITE_POSSIBLE = YES
DEFAULT_REMOTE_TARGET_PRESENT = NO
DEFAULT_LOCAL_TARGET_PRESENT = NO
PYTHON_CAN_BYPASS_HARDENED_DEPLOYER = NO

PYTHON_WRAPPER_DEFAULT_MODE = FAIL_CLOSED（tombstone exit 1；无 preview 委托路径）
EXECUTE_REQUIRES_EXPLICIT_CONFIRMATION = YES（硬化链 contract）
REMOTE_PRECONDITION_ENFORCED = YES（硬化链）
BACKUP_AND_POST_UPLOAD_VERIFY_ENFORCED = YES（硬化链）

PLAINTEXT_SECRET_DETECTED = NO
SECRET_LEAK_COUNT = 0

PYTHON_TEST_MATRIX = PASS（14/14）
POST_CHANGE_WRITE_SURFACE_SCAN = PASS（0 写原语残留）

PRODUCTION_DRIFT_DETECTED = NO
PRODUCTION_WRITE_OCCURRED = NO
FTP_UPLOAD_OCCURRED = NO
BUSINESS_APPLICATION_CODE_MODIFIED = NO
CURRENT_TASK_MODIFIED = NO
FEED_BASELINE_MODIFIED = NO
GIT_COMMIT_OCCURRED = NO
GIT_PUSH_OCCURRED = NO

SYSTEM_WIDE_PRODUCTION_WRITE_SURFACE_HARDENED = YES
OVERALL_DEPLOYMENT_HARDENING_READY_FOR_FINAL_PASS = YES

READY_FOR_GOVERNANCE_STATE_COMMIT = YES
RECOMMENDED_NEXT_ACTION = FYZSXNB-TAKEOVER-GOVERNANCE-STATE-COMMIT-001
```

---

## §40 STOP Gate 遵守声明

已完成：Python legacy closure、测试矩阵、写面重扫、生产只读验证、报告。未执行 git 写、真实 FTP 上传、CURRENT_TASK/Feed Baseline 修改、内容发布、分类/换行治理、Resolver V2/translation-pairs 部署、性能工作。生产零写入。

`GATE_COMPLETE` — 等待审核。