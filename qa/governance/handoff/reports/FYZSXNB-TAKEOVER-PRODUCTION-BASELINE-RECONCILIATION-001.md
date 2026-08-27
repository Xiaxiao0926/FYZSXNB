# FYZSXNB TAKEOVER — Production Baseline Reconciliation Gate 001

**Task ID:** `FYZSXNB-TAKEOVER-PRODUCTION-BASELINE-RECONCILIATION-001`
**性质:** READ-MOSTLY / BASELINE RECOVERY / WORKTREE CLASSIFICATION（零生产写入、零 git 写操作）
**前置 Gate:** Resolver Forensics 001 = FINAL PASS（根因 C）
**执行人:** DeepSeek | **执行日:** 2026-08-26

---

## Executive Summary

2026-08-26 生产实测：**Legacy Resolver v1.3.1（`8aa9aa8a`）+ Feed 插件 v1.2.5（`4997b969`）+ 主题 27 文件（19 与 HEAD 全同，8 为 8-21 后部署且本地工作树=生产、均未提交）**。已建立可信基线 `PROD-BASELINE-20260826-R1`（29 文件 + manifest，独立存放，不覆盖取证快照）。Dirty 工作树 142 项**全量分类完毕：P2=9、L1=1、D1=132、UNKNOWN=0**；Resolver V2 v1.4.0 与 translation-pairs 0.4.0 明确隔离为**UNDEPLOYED DEVELOPMENT**。生产健康：HTTP 9/9=200、无跨语种 feed 泄漏、102 published。`READY_FOR_GIT_CLEANUP = YES`（下一 Gate 执行提交，本 Gate 不做任何 git 写操作）。

---

## 1. Production Snapshot Scope

- 来源：FTP 只读镜像 `ftp.fyzsxnb.com` → `work/tmp/prod-snapshot-0826-baseline/`（独立于取证快照 `prod-snapshot-0826/`）。
- 抓取：**29 个项目自有文件（328,562B）**：
  - mu-plugin 1（`fyzsxnb-p0-seo-patch.php` 21,891B）
  - feed 插件 1（`fyzsxnb-home-dynamic-feeds.php` 26,878B）
  - child theme 27（含 fonts 4、css 3、js 1、inc 2、template-parts/home 8、page-templates 1、根 8）
- 目录清单：`wp-content/plugins/` 中 fyz* 插件仅 `fyzsxnb-home-dynamic-feeds`（**无 translation-pairs**）；`wp-content/mu-plugins/` 仅 FYZSXNB 的 p0-seo-patch + Hostinger 托管商文件 2（未下载第三方）；主题目录全部存活文件（不按历史任务列表筛）。
- 未纳入（按边界）：wp-config.php（凭据红线）、wp-content/uploads（媒体）、第三方插件/主题（Neve 父主题）、WordPress 核心。
- 快照文件清单/哈希：`work/tmp/prod-snapshot-0826-baseline/remote_manifest.json` + `baseline_matrix.json`（字节级 HEAD 比对）。

## 2. Production Version Matrix

| 组件 | 版本 | SHA256 | 大小 | 来源 |
|---|---|---|---|---|
| MU Resolver | **v1.3.1** | `8aa9aa8af1da…` | 21,891B | FTP 实测（= 7-31 实现文件与 8-10 部署记录） |
| Feed 插件 | **v1.2.5** | `4997b9697cf7…` | 26,878B | FTP 实测 |
| Theme style.css | Version 0.3.11 | `281199d403fe…` | 228B | FTP 实测（版本号未随 8-21 后文件改动而 bump） |
| Theme 其余 26 文件 | — | 各自 | 见表 | FTP 实测 |
| Translation-pairs 插件 | — | — | — | **生产 ABSENT**（FTP 550） |

## 3. Production / Worktree / Git Comparison

| State | 数量 | 文件 |
|---|---|---|
| ALL_MATCH（生产=工作树=HEAD） | 19 | theme 18 + feed? （fonts4/css cars-from-china/js/cars-from-china-hub/cta/featured/guides/reading/signals/trust/comments-disabled/front-page/page/README/style/taxonomy） |
| PRODUCTION_EQUALS_WORKTREE（生产=工作树≠HEAD） | 9 | feed v1.2.5 + theme 8（design-system.css、research-wire.css、cars-from-china.php、home.php、desks.php、hero.php、404.php、functions.php） |
| ALL_DIFFERENT | 1 | mu-plugin（生产 v1.3.1 ≠ 工作树 v1.4.0 ≠ HEAD 0.4.1 版） |

主题维度：**生产 27/27 == 工作树（YES）**；**生产 vs HEAD = 19/27（PARTIAL）**。
详细表：`deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/manifest.md`。

## 4. Dirty Worktree Classification（142 项 = 10 modified + 132 untracked）

| 分类 | 数量 | 说明 |
|---|---|---|
| **P1** | 0 | dirty 中无（dirty 均非"三处一致"项） |
| **P2** | 9 | feed v1.2.5 + theme 8 文件：**生产已上线、本地工作树相同、未提交**（真实生产代码，下一 Gate 应提交） |
| **L1** | 1 | `mu-plugins/fyzsxnb-p0-seo-patch.php` v1.4.0（Resolver V2，UNDEPLOYED） |
| **D1** | 132 | docs 103 + qa 28 + reports 1（报告/QA/设计/临时产物） |
| **UNKNOWN** | 0 | — |
| 合计 | 142 | `UNCLASSIFIED_DIRTY_FILES = 0` |

D1 细分：D1-DOC（docs/* 103 + reports/* 1）、D1-QA（qa/* 28，含全部 0.4.x 测试脚本与 045 系列模拟验证脚本——已核：`resolver_v2_production_validation_045F2.py` 等零网络调用，详见 Forensics 报告）。

## 5. Resolver State

- **生产：LEGACY v1.3.1（`8aa9aa8a…`）**——当前唯一事实。
- **本地：V2 v1.4.0（`7c042a45…`，29,819B，含 `FYZ_USE_RESOLVER_V2` flag，默认 false）**——UNDEPLOYED。
- V2 与生产 diff ≈ 331 行；函数级差异见 Forensics 001（§4）。
- `RESOLVER_V2_DEPLOYMENT_STATUS = UNDEPLOYED`（固定，除非生产新证据）。

## 6. Feed Plugin State

- 生产 v1.2.5 = 本地工作树（YES）；生产 ≠ git HEAD（git 内仍为 v1.2.4）。
- 分类 **P2（Production Supporting Work，未提交）**。

## 7. Theme State

- 生产 27/27 = 工作树；19/27 = HEAD（8 文件为 8-21 后部署：design-system.css、research-wire.css、cars-from-china.php、home.php、desks.php、hero.php、404.php、functions.php）。
- 生产 theme 存在 `cars-from-china.css`、`research-wire.js`、`page-templates/cars-from-china-hub.php` 且与 HEAD 一致（此前已提交）。
- 主题版本号（style.css）仍 0.3.11——落后于实际文件内容（记录，未改）。
- 判定完全基于 production hash，未依据任何 Gemini 报告。

## 8. Translation Pairs State

- `TRANSLATION_PAIRS_PRODUCTION = ABSENT`（FTP 550 复测）。
- `TRANSLATION_PAIRS_LOCAL_STATUS = UNDEPLOYED`（插件源码已提交 `3355f3c`，LOCAL_PASS；不属于生产 baseline）。

## 9. CURRENT_TASK Drift

- `CURRENT_TASK_STALE = YES`：文件记录（8-21）为 mu `e31f837b`（无文件对应）、feed 1.2.4（实际 1.2.5）、0.4.5-G running（报告已 PASS 且生产从未 V2）。本 Gate **未修改**（边界）。

## 10. Feed Baseline Drift

- `FEED_BASELINE_STALE = YES`：现有 `feed_036_inventory_report.json`（8-20 基线 96 篇）已不符（现 102 篇；EN signals 54/guides 26、RU signals 26/guides 16）。**本 Gate 未重建**（边界；`FEED_BASELINE_REGEN_REQUIRED = YES`）。
- 无跨语种泄漏观察（EN 候选不含 RU id 等）。

## 11. Deployment Script Hazards

| Script | 默认 RemotePath | 默认 Local/Source | 无参可跑 | 覆盖 | hash verify | 显式确认 | Risk |
|---|---|---|---|---|---|---|---|
| `ftp_p0_deployer.ps1` | **`wp-content/mu-plugins/fyzsxnb-p0-seo-patch.php`** | 未知/必填 | 部分 | 是（默认写 mu-plugin） | 有（deploy 后校验） | 无 | **HIGH** |
| `run_ftp_deploy_secure.ps1` | **同上（默认 mu-plugin）** | 参数化 | 参数缺失即失败 | 取决于调用 | 有 | 无 | **HIGH** |

结论：两脚本默认目标即 mu-plugin——任何调用者漏传 `-RemotePath` 即可能覆盖生产 mu-plugin。未修改脚本（边界）；建议下一 Gate 评估显式强制。

## 12. Existing Deployment Package Gap

- `LATEST_EXISTING_DEPLOYMENT_PACKAGE = fyzsxnb-ui2-036`（0.3.6.1）。
- `LATEST_EXISTING_PACKAGE_MATCHES_PRODUCTION = NO`（生产已有 8-21 后部署的 feed v1.2.5 与 theme 8 文件，且 mu-plugin 与 036 包记录不同）。
- 本 Gate 新建独立 baseline 包（不覆盖旧包）。

## 13. New Trusted Production Baseline

- **ID: `PROD-BASELINE-20260826-R1`**
- `work/deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/`：`manifest.json`（29 文件 × 13 字段，含声明头）、`manifest.md`、`production-summary.md`、`snapshot/`（29 文件镜像）。
- 声明头（manifest 内置）：本基线=2026-08-26 实测生产；不代表本地最新开发态；Resolver V2 v1.4.0 明确排除；045F2/045G 的生产性声明无效。
- 原始快照（工具链）：`work/tmp/prod-snapshot-0826-baseline/`。

## 14. Git Cleanup Readiness

- 判定输入：SNAPSHOT_COMPLETE=YES；UNKNOWN=0；UNCLASSIFIED=0；manifest 完整；V2 隔离为 L1；translation-pairs 隔离为 L1（UNDEPLOYED）。
- `READY_FOR_GIT_CLEANUP = YES` → 下一任务：**`FYZSXNB-TAKEOVER-GIT-BASELINE-CLEANUP-001`**（负责 P2=9 提交、L1=1 以专用分支/标注保留、D1=132 选择性入库或 .gitignore；本 Gate 未执行任何 git add/commit）。

## 15. Recommended Next Action

`FYZSXNB-TAKEOVER-GIT-BASELINE-CLEANUP-001`（待人工/GPT-5.6 审核本报告后放行）：
1. 提交 P2=9（feed v1.2.5 + theme 8：commit 消息注明"生产已部署、与 2026-08-26 基线一致"）；
2. 将 mu-plugin v1.4.0（L1）保留在独立分支或明确标注 UNDEPLOYED，不并入生产基线 commit；
3. D1=132 分类入库（docs/qa/reports 可按组提交或忽略清单）；
4. 可选：修正 `run_ftp_deploy_secure.ps1` 默认 RemotePath 陷阱（另外批准）；
5. 之后：重建 feed baseline、重写 CURRENT_TASK、评估 Resolver V2 全新 Gate。

---

## §31 终值

```text
PRODUCTION_BASELINE_ID = PROD-BASELINE-20260826-R1

PRODUCTION_SNAPSHOT_COMPLETE = YES
PRODUCTION_PUBLISHED_COUNT = 102
PRODUCTION_PENDING_COUNT = 0

CURRENT_PRODUCTION_RESOLVER = LEGACY

PRODUCTION_MU_PLUGIN_VERSION = v1.3.1
PRODUCTION_MU_PLUGIN_SHA256 = 8aa9aa8af1da6d84dc0362c8497e2804d66202fb0368702886b22c3bdb3ed54d

LOCAL_RESOLVER_V2_VERSION = v1.4.0
LOCAL_RESOLVER_V2_SHA256 = 7c042a451544…
RESOLVER_V2_DEPLOYMENT_STATUS = UNDEPLOYED

PRODUCTION_FEED_PLUGIN_VERSION = v1.2.5
PRODUCTION_FEED_PLUGIN_SHA256 = 4997b9697cf7c47abe75bc5fb192e528845acf9a5c4f79c71d13964c45bedb9b

LOCAL_FEED_PLUGIN_MATCHES_PRODUCTION = YES
GIT_FEED_PLUGIN_MATCHES_PRODUCTION = NO

PRODUCTION_THEME_MATCHES_WORKTREE = YES（27/27）
PRODUCTION_THEME_MATCHES_HEAD = PARTIAL（19/27 一致）

TRANSLATION_PAIRS_PRODUCTION = ABSENT
TRANSLATION_PAIRS_LOCAL_STATUS = UNDEPLOYED（已提交 3355f3c，LOCAL_PASS）

CURRENT_TASK_STALE = YES
FEED_BASELINE_STALE = YES

LATEST_GIT_COMMIT = 9feb0ab
COMMITS_AFTER_2026_08_20 = 0

TRACKED_MODIFIED_COUNT = 10
UNTRACKED_COUNT = 132

P1_FILE_COUNT = 0（dirty 中）
P2_FILE_COUNT = 9
L1_FILE_COUNT = 1（dirty 中；另 translation-pairs 已提交未部署）
D1_FILE_COUNT = 132
UNKNOWN_FILE_COUNT = 0

UNCLASSIFIED_DIRTY_FILES = 0

DEPLOYMENT_SCRIPT_HAZARD = HIGH（默认 RemotePath=mu-plugin，未修改）

LATEST_EXISTING_DEPLOYMENT_PACKAGE = fyzsxnb-ui2-036
LATEST_EXISTING_PACKAGE_MATCHES_PRODUCTION = NO

NEW_PRODUCTION_MANIFEST_PATH = work/deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/manifest.json
NEW_PRODUCTION_SNAPSHOT_PATH = work/deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/snapshot/

READY_FOR_GIT_CLEANUP = YES

RECOMMENDED_NEXT_ACTION = FYZSXNB-TAKEOVER-GIT-BASELINE-CLEANUP-001
```

---

## §33 STOP Gate 遵守声明

本任务执行期间：**零生产写入、零 FTP 上传、零 WP/REST 写、零 cache purge、零 git add/commit/reset/checkout/clean/stash、未改 CURRENT_TASK、未重建 feed baseline、未部署任何插件/主题、未发布内容**。唯一落盘输出：`work/tmp/prod-snapshot-0826-baseline/*`、`work/deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/*`、本报告。

`GATE_COMPLETE` — 等待 GPT-5.6 / 人工审核。