# FYZSXNB Feed Parity Baseline — FEED-BASELINE-20260826-R1

> This baseline represents the actual observed FYZSXNB production feed/homepage state on 2026-08-26.

Production Feed Plugin: v1.2.5
Production Resolver: Legacy v1.3.1

Resolver V2 v1.4.0 and translation-pairs 0.4.0 are explicitly excluded because neither is deployed.

This baseline supersedes the stale pre-20260826 feed parity baseline (qa/feed_036_inventory_report.json).

## Content Counts (production 2026-08-26, REST)

| Metric | Value |
|---|---:|
| TOTAL_PUBLISHED | 102 |
| TOTAL_PENDING | 0 |
| EN_PUBLISHED | 76 |
| RU_PUBLISHED | 26 |
| UNKNOWN_LANGUAGE_POSTS | 0 |
| KIND: guide | 48 |
| KIND: signal | 54 |
| MISSING_LANGUAGE_META | 0 |
| INVALID_LANGUAGE_META | 0 |
| MISSING_KIND_META | 0 |
| INVALID_KIND_META | 0 |

## Feed Locale Leakage

| Gate | Count |
|---|---:|
| EN_FEED_RU_POST_COUNT | 0 |
| RU_FEED_EN_POST_COUNT | 0 |
| EN_HOMEPAGE_RU_POST_COUNT | 0 |
| RU_HOMEPAGE_EN_POST_COUNT | 0 |

## Homepage Parity

| Page | Signals | Guides |
|---|---|---|
| EN / | True | True |
| RU /ru/ | True | True |

## Kind Parity (guides section only kind=guide)

PASS=True — signals section = newest locale posts (plugin contract, may include guide-kind); guides section checked for kind=guide

## Featured Deduplication

count=0

## UA Parity (chrome / mobile / googlebot)

EN match=True, RU match=True

## Automotive Posts

| Key | Post | Lang | Kind | Category | Feed candidates |
|---|---|---:|---|---|---|---|
| article_001 | 1098 | en | guide | ru-auto | en-US.signals,en-US.guides |
| article_004 | 1093 | en | guide | ru-auto | en-US.signals,en-US.guides |
| case_001 | 1065 | en | guide | ru-auto | en-US.signals,en-US.guides |
| case_002 | 1077 | en | guide | ru-auto | en-US.signals,en-US.guides |
| case_003 | 1084 | en | guide | ru-auto | en-US.signals,en-US.guides |
| tayron_overview | 640 | ru | guide | china-tech-products,russian-library | ru-RU.signals,ru-RU.guides |

## Taxonomy Anomalies (no feed impact)

5 posts with EN meta but ru-auto category: [1098, 1093, 1084, 1077, 1065]
Classified: TAXONOMY_ANOMALY_WITHOUT_FEED_LEAK (P2 governance debt, do not fix in this gate).

## Cache Contract (source audit, plugin v1.2.5)

key=`fyzsxnb_home_feed_{locale}_{type}_h3` (query version h3, TTL 900s);
invalidation: publish/update/delete/trash/restore, `_fyz_content_*` meta changes, category terms;
LiteSpeed purge via action + class API. CACHE_INVALIDATION_CONTRACT = PASS (code audit only).

## Known Issues / Out of Scope

- /ru/cars-from-china/ html lang=en-US — KNOWN_OUT_OF_SCOPE (not a feed failure)
- 13 legacy posts previously 'unknown language' now carry lang=en (observed at capture; historical metadata debt P3, not re-classified by this gate)
- Automotive EN articles carry ru-auto category (P2 taxonomy governance debt)
- Resolver V2 / translation-pairs not used (both undeployed)

## Gate Values

FEED_PARITY_GATE = PASS
