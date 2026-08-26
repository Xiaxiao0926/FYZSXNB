# Production Summary — PROD-BASELINE-20260826-R1

| Item | Value |
|---|---|
| Resolver | **LEGACY v1.3.1** (`8aa9aa8a…`) |
| Feed plugin | v1.2.5 (`4997b969…`) = local worktree, != HEAD |
| Theme | 27 files; 19 ALL_MATCH + 8 PRODUCTION_EQUALS_WORKTREE |
| Translation pairs plugin | **ABSENT** on production |
| Published / pending | 102 / 0 |
| HTTP health (9 URLs) | 9/9 = 200, canonical self |
| Feed health | EN signals 54 / guides 26; RU signals 26 / guides 16; no cross-locale leak |
| /ru/cars-from-china/ lang | en-US (historical known issue, NOT handled per task boundary) |
| Feeds baseline | stale (needs regeneration, OUT OF SCOPE this gate) |

## Classification of local worktree (142 dirty) 
- P2 = 9 (production-equal code, uncommitted) 
- L1 = 1 (Resolver V2 v1.4.0 mu-plugin, undeployed) 
- D1 = 132 (docs 103 / qa 28 / reports 1) 
- UNKNOWN = 0 
