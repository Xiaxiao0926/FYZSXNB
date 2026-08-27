# FYZSXNB TAKEOVER — Git Baseline Cleanup Gate 001

**Task ID:** `FYZSXNB-TAKEOVER-GIT-BASELINE-CLEANUP-001`
**性质:** LOCAL GIT GOVERNANCE（零生产写入、零 push）
**前置 Gate:** Forensics 001 = PASS | Reconciliation 001 = PASS
**基线:** `PROD-BASELINE-20260826-R1` | **执行人:** DeepSeek | **日期:** 2026-08-26

---

## Executive Summary

Git 已与 2026-08-26 生产基线对齐：commit `a4c87bc` 记录生产 Legacy resolver v1.3.1（index-only 安全写入）、feed 插件 v1.2.5（生产 CRLF 字节）与 8 个已部署 theme 文件（生产字节），并携带 3 份基线 manifest 元数据。**Resolver V2 v1.4.0 未入 commit**（工作树保留、仍 modified——预期）；D1 132 项未混入。HEAD 10 个核心 blob 经 `git rev-parse` 权威验证 **10/10 == 生产快照 blob**。Post-commit dirty = 已知 L1(1) + 已知 D1(132)，UNKNOWN=0。流程中发现并绕过了 `core.autocrlf=true` 的 CRLF→LF 规整洁净问题（P2 全部改用 index-only 生产字节注入）。生产健康复核 6/6=200，零生产写入。

## 1. Pre-Commit Git State

- HEAD=`9feb0ab`（8-20 后零提交）；dirty 142 = modified 10 + untracked 132。
- mu-plugin 三角：HEAD=`803ad333`(0.4.1) ≠ PROD=`8aa9aa8a`(v1.3.1) ≠ WORKTREE=`7c042a45`(v1.4.0) → 方案 B（index-only）。

## 2. P2 Verification

- 从 manifest 提取 9 个 P2，stage 前用 `Get-FileHash`（文件字节）重验：**9/9 Worktree SHA256 == Production SHA256**（feed 4997b969、theme 8 项，MATCH 表见 Staging Plan）。
- 无 NO → 未触发 BLOCKED。

## 3. L1 Protection

- `mu-plugins/fyzsxnb-p0-seo-patch.php`（工作树 v1.4.0）**未直接 add**；采用 Safe Baseline Representation 方案 B：
  - `git hash-object -w` 生产 Legacy 快照 → blob `d9e194a7`；
  - `git update-index --cacheinfo` 注入 index（工作树未触碰）；
  - commit 后 HEAD = 生产 Legacy 记录，工作树 v1.4.0 保留（git status 仍显示 modified——预期且允许）。
- 未采用"先覆盖再恢复"高风险方案。

## 4. D1 Exclusion

- 132 项（docs 103 / qa 28 / reports 1）**一律未 stage**；`D1_ALLOWED_IN_BASELINE_COMMIT = NO` 执行。

## 5. Staging Plan

- 计划落盘：`work/agent-handoff/FYZSXNB-TAKEOVER-GIT-BASELINE-STAGING-PLAN-001.md`（WILL STAGE 10 项 / L1 / D1 / SPECIAL INDEX HANDLING）。

## 6. Staged Diff Audit

- 首轮 `git add` 后 cached 13 项（9 P2 + 3 manifest + mu-plugin index 条目）；审计无 D1/UNKNOWN。
- **发现并处理**：`core.autocrlf=true` 使首轮 add 的 P2 blob 为 LF 规整版（≠ 生产 CRLF 字节）。修正：9 个 P2 全部改用生产快照字节 `hash-object -w` + `update-index --cacheinfo`（与 mu-plugin 同法），`git ls-files -s` 确认 index blob = 生产 blob（feed=`9ba1635c` 等）。
- 通过 `git rev-parse HEAD:<path>`（零内容读取、最权威）验证修正结果。

## 7. Baseline Commit

- `a4c87bc chore: reconcile git with 2026-08-26 production baseline`（message 含基线声明、Legacy 保留、V2/translation-pairs 排除、无生产部署）。
- 内容 13 文件：M mu-plugin（生产 Legacy）、M feed v1.2.5、A 3×manifest 元数据、M 8×theme。raw snapshot 29 文件未提交（`RAW_SNAPSHOT_COMMIT_ALLOWED = NO`）。
- 首版 commit 735c9e1 因 CRLF 规整 blob 不匹配 → `git commit --amend --no-edit` 修正为 a4c87bc（本地未 push，安全）。

## 8. Feed Plugin Reconciliation

- HEAD blob `9ba1635c` == 生产 blob（v1.2.5，26878B CRLF 字节）== 工作树 → `FEED_HEAD_MATCHES_PRODUCTION = YES`。

## 9. Theme Reconciliation

- 8 个此前 PRODUCTION=WORKTREE≠HEAD 的 theme 文件：HEAD blob 与生产 8/8 一致（404.php、design-system.css、research-wire.css、functions.php、cars-from-china.php、home.php、desks.php、hero.php）→ `DEPLOYED_THEME_FILES_HEAD_RECONCILED = 8/8`。

## 10. Resolver V2 Preservation

- HEAD = 生产 Legacy（blob `d9e194a7` = 字节 8aa9aa8a）；工作树 = v1.4.0（`7c042a45`）保留；git status 中 mu-plugin 仍 modified 为**预期状态**（未为"清状态"处理）。`RESOLVER_V2_STILL_LOCAL_UNDEPLOYED = YES`。

## 11. Post-Commit Dirty Tree Classification

- 133 dirty = **1 modified（mu-plugin v1.4.0，L1）** + **132 untracked（D1）**；`UNKNOWN_DIRTY_AFTER_COMMIT = 0`。

## 12. Production Read-Only Health Check

- 6 URL（/、/ru/、/cars-from-china/、/ru/cars-from-china/、Article 004、Article 001）HTTP 全部 200 → git cleanup 未触及生产。

## 13. Remaining Governance Debt

- `core.autocrlf=true` 的换行规整陷阱（本 Gate 以 index-only 规避；仓库级 line-ending 策略待定）。
- 部署脚本默认 RemotePath=mu-plugin（HIGH，未修改）。
- D1 132 项治理（.gitignore/归档）未做（下一阶段）。
- CURRENT_TASK 重写、feed baseline 重建未做（分别留待 FYZSXNB-TAKEOVER-CURRENT-TASK-REWRITE-001 与后续 Gate）。
- `/ru/cars-from-china/` lang=en-US 历史问题记录未处理（边界）。

## 14. Recommended Next Action

`FYZSXNB-TAKEOVER-CURRENT-TASK-REWRITE-001`（CURRENT_TASK.json 以 2026-08-26 生产+基线 commit 为准重写）。

---

## §27 终值

```text
GIT_BASELINE_GATE = PASS

PRODUCTION_BASELINE_ID = PROD-BASELINE-20260826-R1

PREVIOUS_HEAD = 735c9e1（amend 中间态）/ 9feb0ab（Gate 起点）
NEW_HEAD = a4c87bc

BASELINE_COMMIT_CREATED = YES
BASELINE_COMMIT_HASH = a4c87bc
BASELINE_COMMIT_MESSAGE = chore: reconcile git with 2026-08-26 production baseline

P2_EXPECTED_FILE_COUNT = 9
P2_VERIFIED_FILE_COUNT = 9
P2_COMMITTED_FILE_COUNT = 9

STAGED_L1_FILES = 0
STAGED_D1_FILES = 0
STAGED_UNKNOWN_FILES = 0

RESOLVER_V2_STAGED = NO
RESOLVER_V2_STILL_LOCAL_UNDEPLOYED = YES

TRANSLATION_PAIRS_STAGED = NO

FEED_HEAD_MATCHES_PRODUCTION = YES

DEPLOYED_THEME_FILES_EXPECTED = 8
DEPLOYED_THEME_FILES_HEAD_RECONCILED = 8/8

HEAD_PRODUCTION_CODE_ALIGNMENT = COMPLETE

TRACKED_MODIFIED_AFTER_COMMIT = 1
UNTRACKED_AFTER_COMMIT = 132

KNOWN_L1_AFTER_COMMIT = 1（mu-plugin v1.4.0 工作树）
KNOWN_D1_AFTER_COMMIT = 132
UNKNOWN_DIRTY_AFTER_COMMIT = 0

PRODUCTION_WRITE_OCCURRED = NO
GIT_PUSH_OCCURRED = NO
CURRENT_TASK_MODIFIED = NO
FEED_BASELINE_MODIFIED = NO

READY_FOR_CURRENT_TASK_REWRITE = YES
RECOMMENDED_NEXT_ACTION = FYZSXNB-TAKEOVER-CURRENT-TASK-REWRITE-001
```

---

## §30 STOP Gate 遵守声明

本 Gate 执行期间：未 push、未改 CURRENT_TASK、未重建 feed baseline、未动部署脚本、未部署 Resolver V2/translation-pairs、未发布内容、未做性能优化；生产零写入（仅 HTTP GET + FTP 读取复核）。唯一 git 写操作：`git add`（限定 10 路径）、`git update-index --cacheinfo`（生产字节注入）、`git commit`（随后 `--amend` 修正 CRLF 问题，本地未 push）。

`GATE_COMPLETE` — 等待 GPT-5.6 / 人工审核。