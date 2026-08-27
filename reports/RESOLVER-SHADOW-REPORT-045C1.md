# FYZSXNB 0.4.5-C1 — MU Resolver V2 Shadow Comparison Report

**Task ID:** `FYZ-20260820-LANGUAGE-V2-RESOLVER-SHADOW-045C1`  
**Stage:** `0.4.5-C1` (LOCAL SHADOW AUDIT ONLY)  
**Production Status:** `UNCHANGED`  

## 1. 核心指标概览 (Core Metrics)

- **Total Posts Evaluated**: `96`
- **MATCH Count**: `83` (`86.46%`)
- **DIFFERENCE Count**: `13` (`13.54%`)
- **HIGH RISK**: `0`
- **MEDIUM RISK**: `0`
- **LOW RISK**: `13` (全部 13 篇差异均为将历史盲目默认 'en' 提升为精确 'unknown' 隔离)
- **SEO IMPACT**: `0` (影子运行，零公网钩子变更)

## 2. 差异原因深入分析 (Root Cause Analysis)

所有 13 处差异均为存量 13 篇 unknown 文章：
- **旧 Legacy Resolver 行为**：因无 Category 54 且不在旧 ID 表中，直接盲目默认判定为 `en`（存在将中文内容误作为英文输出的缺陷）；
- **新 Resolver V2 行为**：精确识别元数据为空，判定为 `unknown`（`source: none`），触发安全隔离防御；
- **既有 58 篇 EN 与 25 篇 RU**：新旧解析器输出 **100% 逐位匹配**（83/83 MATCH）。

## 3. 全量 96 篇对比明细表 (Full Comparison Ledger)

| Post ID | Slug | Categories | Meta Lang | Old Resolver | New Resolver V2 | Source | Match | Risk |
|---:|:---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| 514 | `volkswagen-tayron-kitay-dq381-avariynyy-` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 513 | `china-market-volkswagen-tayron-dq381-eme` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 512 | `volkswagen-tayron-330tsi-kitay-gpf-opyt-` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 511 | `china-market-volkswagen-tayron-330tsi-gp` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 510 | `volkswagen-tayron-330tsi-dkv-dpl-dth-zap` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 509 | `china-market-volkswagen-tayron-330tsi-dk` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 504 | `byd-frigate-07-openpilot-dannye-dlya-ada` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 503 | `kak-proverit-byd-pered-ustanovkoy-openpi` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 500 | `openpilot-byd-2026-support-open-source` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 496 | `redmagic-cooler-6-pro-plus-china-launch-` | `[53]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 495 | `huawei-matepad-pro-2026-china-announceme` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 494 | `redmi-kids-watch-pro-china-version-buyer` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 493 | `pet-food-label-manufacturer-distributor-` | `[58]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 492 | `datchik-davleniya-4-20ma-2-provoda-wika-` | `[54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 491 | `g1-4-r1-4-npt-datchik-davleniya-rezba-ki` | `[53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 490 | `go-raw-quest-pet-food-recall-thiamine-sa` | `[58]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 489 | `kak-podobrat-datchik-davleniya-4-20ma-dl` | `[53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 487 | `fda-gudid-accessgudid-procurement-verifi` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 486 | `tb-tongue-swab-diagnostic-accuracy-appra` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 485 | `chery-android-auto-obnovlenie-tiggo-7-8-` | `[54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 484 | `proverka-epts-po-vin-pered-pokupkoj` | `[54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 480 | `fda-establishment-registration-device-li` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 479 | `nmpa-udi-2027-class2-devices-ivd-impleme` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 470 | `crp-saa-poct-antibiotic-stewardship-vill` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 466 | `fda-foreign-drug-establishment-registrat` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 465 | `fda-labeler-code-foreign-company-checkli` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 464 | `fxr0906-china-clinical-trial-approval-ev` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 463 | `fda-global-generic-drug-affairs-overseas` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 462 | `fda-drug-registration-listing-compliance` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 461 | `gdufa-iii-controlled-correspondence-deci` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 460 | `cgt-bla-readiness-otp-town-hall-guide` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 451 | `casgevy-pediatric-evidence-extrapolation` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 450 | `glp1-generic-development-pathway-checkli` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 449 | `fda-mie-generic-drug-meeting-checklist` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 448 | `bambu-lab-china-russia-pre-purchase-chec` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 447 | `digitally-derived-endpoints-fda-workshop` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 446 | `fda-2026-cgt-cmc-flexibilities-bla-guide` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 445 | `check-chinese-ivd-russia-registration-re` | `[52, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 444 | `russia-eaeu-ivd-registration-transition-` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 443 | `tb-molecular-test-lod-cfu-10-vs-100` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 442 | `starter-carburetor-chinese-brushcutter-4` | `[53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 441 | `ru-ifind-tbr-evidence-russia-laboratory-` | `[52, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 439 | `ifind-ifq-inh-fluoroquinolone-resistance` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 437 | `ifind-tbr-mtb-rif-cartridge-procurement-` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 435 | `gacc-order-281-special-goods-2026` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 434 | `petmi-vs-russian-cat-food-label-comparis` | `[51, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 433 | `pressure-washer-hose-connector-compatibi` | `[53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 432 | `utilization-fee-china-car-import-russia-` | `[54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 431 | `fully-automated-molecular-poct-system-if` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 426 | `bmw-n55-oil-leak-after-gasket-replacemen` | `[54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 425 | `microplastics-in-pet-food-2026-study-exp` | `[51]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 424 | `national-anti-fraud-center-ai-content-id` | `[50]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 420 | `honor-iz-kitaya-v-rossii-proverka-pered-` | `[50, 53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 415 | `kitayskiy-elektromobil-udalennaya-blokir` | `[50, 54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 413 | `microplastics-in-pet-food-study-methods-` | `[51]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 411 | `china-pharma-exports-2026-formulations-g` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 405 | `ru-bmw-n55-oil-leak-gasket-fkm-nbr` | `[54, 56]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 398 | `ai-voice-recorder-buying-guide-subscript` | `[53]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 394 | `plaud-baseband-engineer-ai-earbuds-signa` | `[50, 55]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 390 | `kitayskiy-korm-dlya-koshek-v-rossii-srav` | `[51, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 388 | `shenzhen-biomed-special-items-import-exp` | `[52]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 372 | `honor-china-vs-eu-version-russia-guide` | `[50, 53, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 362 | `best-budget-robot-vacuum-2026-reddit-gui` | `[53]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 361 | `cat-lickable-supplements-calming-nutriti` | `[51]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 360 | `best-27-inch-ips-monitor-under-cad-300` | `[53]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 359 | `bristol-myers-ai-factory-samsung-biologi` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 358 | `waic-2026-agent-phone-robots-product-sig` | `[50]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 357 | `tempus-personalis-mrd-cancer-testing-dea` | `[52]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 356 | `pedigree-wet-dog-food-recall-safety-chec` | `[51]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 355 | `xiaomi-mijia-water-flosser-pro-product-s` | `[50]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 350 | `kimi-k3-ru-open-model` | `[50, 54]` | `ru` | `ru` | `ru` | `meta` | YES | NONE |
| 349 | `kimi-k3-open-weight-model` | `[50]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 347 | `kimi-k3-zhihu-open-source-model` | `[50]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 304 | `ditch-the-kibble-my-secret-to-healthy-ho` | `[21]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 297 | `the-ultimate-cat-wooden-jigsaw-puzzle-a-` | `[21, 42]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 290 | `healing-crystal-cat-ornaments-a-guide-to` | `[42, 45]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 287 | `bring-whimsical-charm-to-your-space-with` | `[42]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 284 | `the-grim-reaper-on-a-toilet-statue-your-` | `[42]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 276 | `shan-gui-hua-qian-the-ultimate-guide-to-` | `[45]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 268 | `dog-fur-memorial-pendant-carry-their-ete` | `[21]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 257 | `unlock-abundance-the-ancient-wealth-amul` | `[45]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 250 | `how-to-decorate-your-desk-with-leon-the-` | `[42]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 239 | `how-to-buy-from-taobao-directly-a-step-b` | `[39]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 233 | `legacy-of-the-dragon-tomb-chapter-8` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 226 | `how-to-choose-the-best-keepsake-for-pet-` | `[21]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 222 | `legacy-of-the-dragon-tomb-chapter-7` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 219 | `legacy-of-the-dragon-tomb-chapter-6` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 217 | `legacy-of-the-dragon-tomb-chapter-5` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 215 | `legacy-of-the-dragon-tomb-chapter-4` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 213 | `%e8%b7%a8%e5%a2%83%e7%94%b5%e5%95%86%e4%` | `[33]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 209 | `%e8%b7%a8%e5%a2%83%e7%94%b5%e5%95%86%e4%` | `[33]` | `-` | `en` | `unknown` | `none` | **NO** | LOW (Beneficial Unknown Flag) |
| 206 | `legacy-of-the-dragon-tomb-chapter-3` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 204 | `legacy-of-the-dragon-tomb-chapter-2` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 195 | `legacy-of-the-dragon-tomb` | `[32]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 193 | `wie-sie-ihre-katze-davon-abhalten-auf-ih` | `[21]` | `en` | `en` | `en` | `meta` | YES | NONE |
| 52 | `how-to-stop-your-cat-from-sitting-on-you` | `[21]` | `en` | `en` | `en` | `meta` | YES | NONE |
