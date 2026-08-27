# FYZSXNB TAKEOVER — Git Baseline Staging Plan 001

**Task:** `FYZSXNB-TAKEOVER-GIT-BASELINE-CLEANUP-001` | **Baseline:** `PROD-BASELINE-20260826-R1`
**Date:** 2026-08-26 | **性质:** 本地 git 治理（零生产写入、零 push）

## WILL STAGE（10 项 = 9 P2 生产代码 + 3 baseline manifest 元数据）

> P2 哈希已于 stage 前重验 **9/9 MATCH**（worktree SHA256 == PROD-BASELINE-20260826-R1 SHA256）。

| # | Repo path | 来源 |
|---|---|---|
| 1 | `plugin/fyzsxnb-home-dynamic-feeds/fyzsxnb-home-dynamic-feeds.php` | 生产 v1.2.5 (`4997b969`) |
| 2 | `theme/fyzsxnb-neve-child/assets/css/design-system.css` | 生产 (`b898da7f`) |
| 3 | `theme/fyzsxnb-neve-child/assets/css/research-wire.css` | 生产 (`5dfc2fef`) |
| 4 | `theme/fyzsxnb-neve-child/inc/cars-from-china.php` | 生产 (`c86250af`) |
| 5 | `theme/fyzsxnb-neve-child/inc/home.php` | 生产 (`d15f32fb`) |
| 6 | `theme/fyzsxnb-neve-child/template-parts/home/desks.php` | 生产 (`0fbfbce6`) |
| 7 | `theme/fyzsxnb-neve-child/template-parts/home/hero.php` | 生产 (`fd3be590`) |
| 8 | `theme/fyzsxnb-neve-child/404.php` | 生产 (`351ac848`) |
| 9 | `theme/fyzsxnb-neve-child/functions.php` | 生产 (`3acf887a`) |
| 10 | `qa/baselines/PROD-BASELINE-20260826-R1/{manifest.json, manifest.md, production-summary.md}` | 从 `work/deployments/FYZSXNB-PRODUCTION-BASELINE-20260826/` 复制（元数据副本；raw snapshot 29 文件 **不提交**） |

## WILL NOT STAGE — L1（1 项）

- `mu-plugins/fyzsxnb-p0-seo-patch.php`（工作树 = Resolver V2 v1.4.0 `7c042a45`）——**绝不直接 add**。
- `plugin/fyzsxnb-translation-pairs/…`（已提交 3355f3c，UNDEPLOYED）——保持现状，不纳入。

## SPECIAL INDEX HANDLING（mu-plugin 方案 B：index-only baseline）

三角关系：HEAD=`803ad333`（0.4.1 冻结版）≠ PRODUCTION=`8aa9aa8a`（Legacy v1.3.1）≠ WORKTREE=`7c042a45`（V2 v1.4.0）。

采用安全 index-only 路径（不碰工作树）：
1. `git hash-object -w` 生产 Legacy 快照文件（来自 baseline snapshot）；
2. `git update-index --cacheinfo 100644,<blob>,mu-plugins/fyzsxnb-p0-seo-patch.php`；
3. commit 后：HEAD = 生产 Legacy 记录；工作树 v1.4.0 保留（git status 继续显示 modified —— **预期且允许**）；
4. 禁止"先覆盖工作树再恢复"的高风险方案。

## WILL NOT STAGE — D1（132 项）

- docs/ 103、qa/ 28、reports/ 1（QA/报告/设计/临时产物）——一律不进 baseline commit。

## Stage 后验收（§13/§14）

```text
STAGED_L1_FILES = 0（mu-plugin index 条目为生产 Legacy blob，非 v1.4.0）
STAGED_D1_FILES = 0
STAGED_UNKNOWN_FILES = 0
P2_HASH_MATCH = 100%（9/9 已在计划前重验）
RESOLVER_V2_STAGED = NO
TRANSLATION_PAIRS_STAGED = NO
PRODUCTION_WRITE_OCCURRED = NO
```

`PLAN_READY` —— 经人工/GPT-5.6 认可后执行（或作为执行书自带步骤直接执行）。