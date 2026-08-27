# FYZSXNB Automotive Article 002 — Draft Revision Report 001

**Task:** `FYZSXNB-AUTOMOTIVE-ARTICLE-002-DRAFT-REVISION-001`
**v1 source commit:** `8255510` | **本 Gate:** 定向收紧修订（不推倒重写）
**Status:** `DRAFT_REVISION_COMPLETE` / `READY_FOR_GPT_REVIEW`（非发布批准）

## 逐条对照表

| GPT Review Item | v1 | v2 | Status |
|---|---|---|---|
| Russia law status | outdated ("debate") | updated: **266-ФЗ enacted 26.07.2026, published 03.08.2026**; implementation noted as still forming | PASS |
| Tier 1 classification | AAA/I-CAR/RDN mis-tiered | corrected: Tier1=OEM/官网/法规/监管/原始文件；AAA/I-CAR→Tier2；RDN→Tier3；266-ФЗ 官方文本→真正 Tier1 | PASS |
| four-year import claim | present | removed | PASS |
| fleet repair inference | overreach (windshield rate) | downgraded to exposure statement; explicit "no mileage/repair/ADAS-volume claim" | PASS |
| national coverage inference | "patchy / capitals / alignment" | removed; replaced with "no official national coverage statistics; directional observations" | PASS |
| "verification layer does not exist" | present | replaced: "uneven access... available evidence points to uneven" | PASS |
| parallel/semi-official channels | present | removed | PASS |
| equipment-vs-training comparison | "equipment ahead of procedures" | downgraded: "visibly available... procedure access, model coverage and training remain separate constraints" | PASS |
| Chinese-vs-open-brand comparison | "harder than brands with open data" | removed; replaced with "limited or fragmented access... can complicate" | PASS |
| "obsolete within weeks" rhetoric | present | replaced with model/hardware/software/OEM-procedure generalisation statement | PASS |
| "缺铁缺手册" rhetoric | present | replaced: "Hardware availability alone does not solve the data, procedure and training problem." | PASS |
| technical camera wording | "primary lane-keeping/AEB/TSR" | corrected: "a common input for lane-support and sign-recognition functions and, depending on system architecture, forward safety functions" | PASS |
| VIN calibration wording | "OEM-defined parameters for that specific VIN" | corrected: "OEM-defined procedures and configuration data for the relevant model or vehicle configuration" | PASS |
| Source count mismatch | Gate=21 / Draft=21 / Ledger=23 | resolved: **25 used + 2 supplemental (27 total)**, draft cites S1–S25 explicitly, supplemental S26/S27 not cited | PASS |
| Ledger limitation fields | absent | v2 adds What-it-proves / What-it-does-NOT-prove / Used-in-draft per row; service/vendor/community limitations written | PASS |
| Visual Figure 2 | "gap" labels | "Potential Capability Constraints" + "not a quantified national gap assessment" | PASS |
| Visual Figure 3 | "Solution Stack" | "Potential China–Russia Aftermarket Support Architecture" + directional caption | PASS |
| SEO description | "expose an aftermarket calibration gap" | downgraded: "...creating new ADAS calibration demands — and where aftermarket capability gaps may lie" | PASS |
| Boundary sentences | present (3/4 exact) | all four present: "model- and OEM-specific" ✓ "This is not a repair instruction." ✓ "Equipment coverage does not equal verified OEM procedure coverage." ✓ "No Chinese-OEM-specific calibration parameter is generalized here." ✓ | PASS |

## 法律核验（266-ФЗ）

- **已正式通过**：Федеральный закон от 26.07.2026 № 266-ФЗ（修改《消费者权益保护法》第 6 条）——官方文本 kremlin.ru/acts/bank/53474 [S15]
- **公布**：Российская газета 2026-08-03 [S16]
- **义务范围**：制造商须提供维修/维护所需技术文档（ГАРАНТ [S17]、Lidings [S19]、autonews [S18] 确认汽车业关联）
- **实施细则**：请求方式/文档格式/执法实践等操作细节仍在形成 —— C4B 以 Medium confidence 表述
- **边界**：法律 ≠ 每台中国 VIN 的 ADAS 程序即时可用（C4C）——正文 150–220 词小段落在 §4，未膨胀为法律分析

## Claim-to-Draft Audit（§33）

| Claim | Draft location | Source | Status |
|---|---|---|---|
| C1 6% parc | §1 intro | S1 | ✓ |
| C2 fleet exposure（降级） | §1 | S2 | ✓ |
| C3 СТО struggle | §1/.4 | S3,S4,S5,S14 | ✓ |
| C4A 266-ФЗ enacted | §4 legal box | S15,S16,S17,S19 | ✓ 新增 |
| C4B implementation forming | §4 legal box | S16,S18,S19 | ✓ 新增 |
| C4C not proof of per-VIN availability | §4 legal box | synthesis | ✓ |
| C5 archetypal trigger principle | §2/§3 | S9-S12 | ✓ |
| C6 model-level OE policies（BMW/MINI trade report） | §3/§4 | S13 | ✓ Tier3 标注 |
| C7 systems discipline | §2 | S9,S10,S20-S25 | ✓ |
| C8 equipment availability | §4 | S20-S25 | ✓ 比较级已删 |
| C9 emerging service layer（directional） | §1/§4 | S6,S7,S8 | ✓ 覆盖率声明 |
| C10 Zeekr Moscow service | §5 | S7 | ✓ single-listing 边界 |
| C11 one trade source training/payback | §4 | S8 | ✓ Medium/directional |
| C12 model-dependence framing | §3/§5 | S9,S11,S13 | ✓ |
| C13 no official coverage statistic identified | §1/FAQ/报告 | research result | ✓ Medium |
| C14 potential capability stack（directional） | §6 | synthesis | ✓ |

审计结果：**PASS**（16/16 claims 全部映射；无孤立强判断；定量/法律/市场/比较/OEM 类 claim 均可溯源）。

## 比较词/绝对词检查（§34/§35）

- 扫描 17 词（more/less/harder/ahead/most/primary/widespread 等 + always/never/all/every/must/cannot）：命中均核验语境——FAQ 问题词（widespread/always）、位置词（behind×3）、产品名（Autel All-Systems）、研究限制自述（primary/only）、安全边界（every/must/cannot）；".most visibly" 已降级为 "frequently"。
- 绝对值仅存于技术安全边界（"Every calibration requirement is model- and OEM-specific"）。

## 红线（§32 复验）

`UNIVERSAL_CALIBRATION_PARAMETERS = 0`（扫描 mm/距离/角度/road-test/coding/service-menu 全空）
`FABRICATED_CASES = 0` · `INVENTED_SEARCH_VOLUME = 0` · `STEP_BY_STEP_INSTRUCTIONS = 0`

## 文件清单（v1 全部保留，新增 v2 系列）

1. `docs/FYZSXNB-AUTOMOTIVE-ARTICLE-002-ADAS-CALIBRATION-DRAFT-002.md`（~2,100 词正文 + 27 条来源）
2. `docs/FYZSXNB-AUTOMOTIVE-ARTICLE-002-SOURCE-LEDGER-V2.md`（25 used + 2 supplemental；Tier 修正；limitation 字段）
3. `docs/FYZSXNB-AUTOMOTIVE-ARTICLE-002-CLAIM-LEDGER-V2.md`（C4A/B/C 新增；C2/C11/C13 修订）
4. `docs/FYZSXNB-AUTOMOTIVE-ARTICLE-002-VISUAL-PLAN-V2.md`（Fig2/Fig3 措辞修订）
5. `docs/FYZSXNB-AUTOMOTIVE-ARTICLE-002-SEO-DRAFT-V2.md`（description 降级）
6. 本报告

## 关键研究结论

- `RUSSIA_266FZ_STATUS_VERIFIED = YES`（enacted 26.07.2026 / gazette 03.08.2026）
- `IMPLEMENTATION_RULE_STATUS_VERIFIED = YES`（操作细节仍在形成，正文按此表述）
- `TRUE_TIER1_OEM_SOURCE_ADDED = YES`（266-ФЗ 官方文本 + 官方公报）
- `CHINESE_OEM_PRIMARY_SOURCE_FOUND = NO`（公开渠道无中国 OEM 原始服务程序文档；第三方/社区页不构成 OEM 源）→ 明确 `NOT IDENTIFIED`，不阻塞，文章保持市场/系统分析定位

**STOP** — 交 GPT-5.6 第二轮终审（factual/source/overclaim/Russian-market/visual/metadata）。发布权限仍归 GPT-5.6。Git backup 见下方 commit/push 记录。