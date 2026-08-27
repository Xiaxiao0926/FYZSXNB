# FYZSXNB TAKEOVER — Feed Parity Baseline Regeneration Gate 001

**Task ID:** `FYZSXNB-TAKEOVER-FEED-PARITY-BASELINE-REGEN-001`
**性质:** READ-ONLY PRODUCTION FEED QA / BASELINE REGENERATION（零生产写入、零 git 写）
**前置 Gate:** Forensics 001 / Reconciliation 001 / Git Cleanup 001 / CURRENT_TASK Rewrite 001 = FINAL PASS
**执行人:** DeepSeek | **执行日期基线:** 2026-08-26

---

## Executive Summary

以生产实测（REST + 首页 HTML × 3 UA + 插件源码审计）重新钉死 **FEED-BASELINE-20260826-R1**：102 published / 0 pending（EN 76 / RU 26），metadata contract 100% 合规，**四项泄漏 Gate 全部 0**，EN/RU 首页与 Feed 查询逐位一致，guides 区 kind 纯化，featured 0 重复，3 UA 输出一致，6 篇 Automotive 内容全部进入预期 Feed 位置（含 640 RU overview），健康 6/6=200，缓存契约（v1.2.5，key `…{locale}_{type}_h3`）源码确认完整。记录两项非阻塞观察：13 篇历史 unknown 现已带 lang=en（P3 债务）、5 篇 EN Automotive 文章带 ru-auto category（P2 taxonomy 债务，**无 feed 泄漏**）。`FEED_PARITY_GATE = PASS`，未修改任何业务代码。

## 1. Baseline Sources

- CURRENT_TASK.json（feed 1.2.5 / Legacy v1.3.1 / 102×0 / next=本 Gate）
- `PROD-BASELINE-20260826-R1/manifest.json`（feed 插件 1.2.5 生产哈希）
- 生产只读：REST /posts（102）、QA /feed-state、EN/RU 首页 × chrome/mobile/googlebot、6 健康 URL、6 automotive posts、插件 v1.2.5 源码（本地=生产）

## 2. Production Content Inventory

- `production-content-index.json`（全 102 篇 id/slug/title/date/categories/meta + 4 feed 候选有序清单）
- 102 published / 0 pending；EN 76 / RU 26 / UNKNOWN 0（观察：8-20 时 13 unknown 现均带 `lang=en`——历史 metadata 债务 P3，本 Gate 不重判）

## 3. Publication Metadata Contract

- MISSING_LANGUAGE=0、INVALID_LANGUAGE=0、MISSING_KIND=0、INVALID_KIND=0；kind: guide 48 / signal 54（全 102 篇双 meta 齐备）

## 4. EN Feed Results

- signals 4（1098 / 1093 / 1084 / 1077）、guides 6（1065 + 5 FDA/召回 guide）——全部 `lang=en`，0 RU。

## 5. RU Feed Results

- signals 4（640 / 514 / 512 / 510）、guides 6（503 / 489 / 485 / 484 / 448 / 445 序）——全部 `lang=ru`，0 EN；640（Tayron overview）正确位居 RU signals 首位。

## 6. Homepage Parity

- EN `/`：signals ✓ guides ✓（与 feed 查询渲染一致）；RU `/ru/`：signals ✓ guides ✓ → `HOMEPAGE_EN_PARITY = PASS`、`HOMEPAGE_RU_PARITY = PASS`。

## 7. Signal / Guide Parity

- guides 区全部 kind=guide（0 污染）；signals 区 = 最新 locale 文章（插件契约如此，可含 guide-kind）→ `SIGNAL_GUIDE_KIND_PARITY = PASS`。

## 8. Featured Deduplication

- EN featured（人工精选）与 guides 交集 = **0**。

## 9. Automotive Content Verification

| Post | Lang/Kind | Category | Feed 候选 | 状态 |
|---|---|---:|---|---|
| 1098（Article 001） | en/guide | ru-auto | en-US.signals+guides | ✅ |
| 1093（Article 004） | en/guide | ru-auto | en-US.signals+guides | ✅ |
| 1084（Case 003） | en/guide | ru-auto | en-US.signals+guides | ✅ |
| 1077（Case 002） | en/guide | ru-auto | en-US.signals+guides | ✅ |
| 1065（Case 001） | en/guide | ru-auto | en-US.signals+guides | ✅ |
| 640（Tayron overview） | ru/guide | china-tech+russian-library | ru-RU.signals+guides | ✅ |

Article 001/004 已实际占据 EN signals 前两位（首屏动态验证通过）。

## 10. Taxonomy Findings

- `AUTOMOTIVE_TAXONOMY_ANOMALY = PRESENT`（5 篇 EN Automotive 文 category=ru-auto [ID 56]，language meta=en，EN feed 正常、无 RU 泄漏）→ 归类 `TAXONOMY_ANOMALY_WITHOUT_FEED_LEAK`（P2 债务，本 Gate 不修）。

## 11. Multi-UA / Cache Parity

- 3 UA（Desktop Chrome / iPhone Safari / Googlebot）× EN/RU 首页：signals+guides slug 序列**完全一致**（UA_PARITY = PASS）；LiteSpeed 缓存命中下无跨 UA 内容分叉。

## 12. Feed Cache Contract

- 源码（v1.2.5）：key `fyzsxnb_home_feed_{locale}_{type}_h3`（locale+type+query version），TTL 900s；失效钩子：save_post_post / trashed / untrashed / before_delete / `_fyz_content_*` meta 变更 / set_object_terms + LiteSpeed purge（action+class API）→ `CACHE_INVALIDATION_CONTRACT = PASS`（仅代码审计，未发布测试文章）。

## 13. Known Issues / Out-of-Scope

- `/ru/cars-from-china/` lang=en-US — `KNOWN_OUT_OF_SCOPE`（不计入 Feed Fail）。
- 13 历史 unknown → lang=en（P3 债务，未改）。
- EN Automotive + ru-auto category（P2 债务，未改）。
- Resolver V2 / translation-pairs 完全未参与（均未部署）。

## 14. New Feed Baseline

- ID `FEED-BASELINE-20260826-R1` → `work/qa/FYZSXNB-FEED-PARITY-BASELINE-20260826-R1/`（baseline.json/.md、production-content-index、homepage-parity、locale-parity、metadata-contract、ua-cache-parity、raw/ 五组原始捕获 + 三个临时脚本）。
- 旧基线 `qa/feed_036_inventory_report.json`（8-20，96 篇，feed 1.2.4 时代）**保留不删**，声明 `SUPERSEDED_BY = FEED-BASELINE-20260826-R1`。
- baseline.md 头部含 §35 声明（V2/translation-pairs 明确排除、supersede 声明）。

## 15. Scope / Write Audit

- 生产零写入（仅 REST GET / HTTP GET / 源码读取）；git 前后一致（133 = 1 modified L1 + 132 untracked D1，零新增业务 diff）；CURRENT_TASK 未动；新文件仅限 baseline 目录（仓库外 `work/qa/`）。

## 16. Recommended Next Action

`FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-001`（消掉部署脚本默认写 mu-plugin 的 HIGH 风险；期间仍不恢复内容发布）。

---

## §45 终值

```text
FEED_PARITY_GATE = PASS

FEED_PARITY_BASELINE_ID = FEED-BASELINE-20260826-R1
NEW_FEED_BASELINE_PATH = work/qa/FYZSXNB-FEED-PARITY-BASELINE-20260826-R1/
OLD_FEED_BASELINE_STATUS = SUPERSEDED（旧: qa/feed_036_inventory_report.json, 2026-08-20, 96 posts, feed 1.2.4 时代）

PRODUCTION_BASELINE = PROD-BASELINE-20260826-R1
GIT_BASELINE = a4c87bc
PRODUCTION_FEED_PLUGIN_VERSION = 1.2.5
PRODUCTION_RESOLVER = LEGACY_V1.3.1

TOTAL_PUBLISHED = 102
TOTAL_PENDING = 0
EN_PUBLISHED = 76
RU_PUBLISHED = 26
UNKNOWN_LANGUAGE_POSTS = 0（观察：13 legacy unknown 现带 lang=en, P3 债务）

EN_SIGNAL_COUNT = 4（渲染）/ 54 候选
EN_GUIDE_COUNT = 6（渲染）/ 26 候选
RU_SIGNAL_COUNT = 4（渲染）/ 26 候选
RU_GUIDE_COUNT = 6（渲染）/ 16 候选

MISSING_LANGUAGE_META = 0
INVALID_LANGUAGE_META = 0
MISSING_KIND_META = 0
INVALID_KIND_META = 0

EN_FEED_RU_POST_COUNT = 0
RU_FEED_EN_POST_COUNT = 0
EN_HOMEPAGE_RU_POST_COUNT = 0
RU_HOMEPAGE_EN_POST_COUNT = 0

EN_HOMEPAGE_FEED_PARITY = PASS
RU_HOMEPAGE_FEED_PARITY = PASS
SIGNAL_GUIDE_KIND_PARITY = PASS

FEATURED_DUPLICATE_COUNT = 0

AUTOMOTIVE_ARTICLE_001_FEED_STATUS = PUBLISHED / en-US.signals+guides
AUTOMOTIVE_ARTICLE_004_FEED_STATUS = PUBLISHED / en-US.signals+guides
AUTOMOTIVE_CASE_001_FEED_STATUS = PUBLISHED / en-US.signals+guides
AUTOMOTIVE_CASE_002_FEED_STATUS = PUBLISHED / en-US.signals+guides
AUTOMOTIVE_CASE_003_FEED_STATUS = PUBLISHED / en-US.signals+guides

AUTOMOTIVE_TAXONOMY_ANOMALY = PRESENT（无 feed 泄漏, P2 债务）
AUTOMOTIVE_TAXONOMY_ANOMALY_POST_IDS = [1065, 1077, 1084, 1093, 1098]

UA_PARITY = PASS
CACHE_CONTRACT = PASS（v1.2.5 源码审计: key h3, TTL 900s, 失效钩子全）

RU_CARS_HUB_LANG_DEFECT = KNOWN_OUT_OF_SCOPE
RESOLVER_V2_USED = NO
TRANSLATION_PAIRS_USED = NO

PRODUCTION_WRITE_OCCURRED = NO
BUSINESS_CODE_MODIFIED = NO
CURRENT_TASK_MODIFIED = NO
GIT_COMMIT_OCCURRED = NO
GIT_PUSH_OCCURRED = NO

READY_FOR_DEPLOYMENT_HARDENING = YES
RECOMMENDED_NEXT_ACTION = FYZSXNB-TAKEOVER-DEPLOYMENT-HARDENING-001
```

---

## §49 STOP Gate 遵守声明

本 Gate 仅产出：新 feed baseline 目录（baseline.json/.md + 5 分片 JSON + raw/ 捕获 + 3 个临时只读脚本）与本报告。未做部署加固、CURRENT_TASK 更新、git 提交、feed/taxonomy 修复、Resolver V2/translation-pairs 任何动作、内容发布、性能工作。生产零写入。

`GATE_COMPLETE` — 等待审核。