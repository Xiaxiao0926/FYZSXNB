# FYZSXNB 0.4.5-E Phase 2 — Internal Canary Live Comparison Report

**Task ID:** `FYZ-20260820-LANGUAGE-V2-PRODUCTION-CANARY-INTERNAL-045E2`  
**Stage:** `0.4.5-E Phase 2` (PRODUCTION INTERNAL CANARY)  
**Access Gate:** `current_user_can('manage_options')` + `X-FYZ-Resolver-V2: 1`  
**Cache Safety:** `DONOTCACHEPAGE = true`  

## 1. 30 篇重点样本 Legacy vs Internal Canary V2 逐篇比对表

| Post ID | Slug | Meta | Legacy Locale | Canary V2 Locale | Legacy HTML Lang | Canary V2 HTML Lang | Status |
|---:|:---|:---:|:---:|:---:|:---:|:---:|:---:|
| 513 | `china-market-volkswagen-tayron-dq38` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 511 | `china-market-volkswagen-tayron-330t` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 509 | `china-market-volkswagen-tayron-330t` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 496 | `redmagic-cooler-6-pro-plus-china-la` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 495 | `huawei-matepad-pro-2026-china-annou` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 494 | `redmi-kids-watch-pro-china-version-` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 493 | `pet-food-label-manufacturer-distrib` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 490 | `go-raw-quest-pet-food-recall-thiami` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 487 | `fda-gudid-accessgudid-procurement-v` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 486 | `tb-tongue-swab-diagnostic-accuracy-` | `en` | `en` | `en` | `en-US` | `en-US` | MATCH |
| 514 | `volkswagen-tayron-kitay-dq381-avari` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 512 | `volkswagen-tayron-330tsi-kitay-gpf-` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 510 | `volkswagen-tayron-330tsi-dkv-dpl-dt` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 504 | `byd-frigate-07-openpilot-dannye-dly` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 503 | `kak-proverit-byd-pered-ustanovkoy-o` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 500 | `openpilot-byd-2026-support-open-sou` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 492 | `datchik-davleniya-4-20ma-2-provoda-` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 491 | `g1-4-r1-4-npt-datchik-davleniya-rez` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 489 | `kak-podobrat-datchik-davleniya-4-20` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 485 | `chery-android-auto-obnovlenie-tiggo` | `ru` | `ru` | `ru` | `ru-RU` | `ru-RU` | MATCH |
| 479 | `nmpa-udi-2027-class2-devices-ivd-im` | `` | `en` | `unknown` | `en-US` | `en-US` | MATCH |
| 470 | `crp-saa-poct-antibiotic-stewardship` | `` | `en` | `unknown` | `en-US` | `en-US` | MATCH |
| 444 | `russia-eaeu-ivd-registration-transi` | `` | `en` | `unknown` | `en-US` | `en-US` | MATCH |
| 435 | `gacc-order-281-special-goods-2026` | `` | `en` | `unknown` | `en-US` | `en-US` | MATCH |
| 213 | `%e8%b7%a8%e5%a2%83%e7%94%b5%e5%95%8` | `` | `en` | `unknown` | `en-US` | `en-US` | MATCH |
| 9021 | `mock-zh-signal-biomed` | `zh` | `en` | `zh` | `en-US` | `zh-CN` | UPGRADE (en-US -> zh-CN) |
| 9022 | `mock-zh-guide-udi` | `zh` | `en` | `zh` | `en-US` | `zh-CN` | UPGRADE (en-US -> zh-CN) |
| 9031 | `mock-conflict-zh-cat54` | `zh` | `ru` | `zh` | `ru-RU` | `zh-CN` | UPGRADE (ru-RU -> zh-CN) |
| 9032 | `mock-conflict-ru-no-cat54` | `ru` | `en` | `ru` | `en-US` | `ru-RU` | UPGRADE (en-US -> ru-RU) |
| 9033 | `mock-conflict-en-cat54` | `en` | `ru` | `en` | `ru-RU` | `en-US` | UPGRADE (ru-RU -> en-US) |
