import json, os, re

TARGET_IDS = [479, 470, 444, 435, 424, 411, 394, 388, 358, 355, 347, 213, 209]

# Load snapshot
with open(r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\qa\LOCALE-PRODUCTION-META-SNAPSHOT-041.json", "r", encoding="utf-8") as f:
    snapshot = json.load(f)

snap_map = {p["post_id"]: p for p in snapshot}

# Load titles from feed_036_inventory_report.json
titles_map = {}
with open(r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\qa\feed_036_inventory_report.json", "r", encoding="utf-8") as f:
    inv = json.load(f)
    for p in inv.get("migration", []):
        titles_map[p["id"]] = p.get("title", "")

evidence_results = {}

for pid in TARGET_IDS:
    p = snap_map[pid]
    slug = p["slug"]
    cats = p["categories"]
    title = titles_map.get(pid, "")

    evidence_results[pid] = {
        "post_id": pid,
        "slug": slug,
        "title": title,
        "categories": cats,
        "current_language": p.get("content_language", ""),
        "current_kind": p.get("content_kind", ""),
        "language_evidence": [],
        "kind_evidence": [],
        "language_level": "LEVEL D — NO EVIDENCE",
        "kind_level": "LEVEL D — NO EVIDENCE",
        "proposed_language": "",
        "proposed_kind": "",
        "write_eligible": False,
        "notes": "",
    }

# Specific manual audits for each post based on file findings:

# 1. Post 479: nmpa-udi-2027-class2-devices-ivd-implementation-guide
# File: work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-udi-ivd-2027/nmpa-udi-2027-class2-devices-ivd-implementation-guide.seo.json
# Stated: language: "zh", article_type: "biomed_regulatory_guide"
evidence_results[479]["language_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-udi-ivd-2027/nmpa-udi-2027-class2-devices-ivd-implementation-guide.seo.json",
    "record": 'Explicit field: "language": "zh"'
})
evidence_results[479]["kind_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-udi-ivd-2027/nmpa-udi-2027-class2-devices-ivd-implementation-guide.seo.json",
    "record": 'Explicit field: "article_type": "biomed_regulatory_guide"'
})
evidence_results[479]["language_level"] = "LEVEL A — AUTHORITATIVE (zh)"
evidence_results[479]["kind_level"] = "LEVEL A — AUTHORITATIVE (guide)"
evidence_results[479]["proposed_language"] = "zh"
evidence_results[479]["proposed_kind"] = "guide"
evidence_results[479]["write_eligible"] = False # Schema only supports en / ru
evidence_results[479]["notes"] = "Language is explicitly 'zh' (Chinese). Under current EN/RU publication schema, zh is not supported in _fyz_content_language. NO_WRITE."

# 2. Post 470: crp-saa-poct-antibiotic-stewardship-village-clinics
# File: work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-crp-saa-poct-stewardship/crp-saa-poct-antibiotic-stewardship-village-clinics.seo.json
# Stated: language: "zh", article_type: "biomed_clinical_evidence"
evidence_results[470]["language_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-crp-saa-poct-stewardship/crp-saa-poct-antibiotic-stewardship-village-clinics.seo.json",
    "record": 'Explicit field: "language": "zh"'
})
evidence_results[470]["kind_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260807-PUBLISH10-BIOMED-A-001/manual-biomed-crp-saa-poct-stewardship/crp-saa-poct-antibiotic-stewardship-village-clinics.seo.json",
    "record": 'Explicit field: "article_type": "biomed_clinical_evidence"'
})
evidence_results[470]["language_level"] = "LEVEL A — AUTHORITATIVE (zh)"
evidence_results[470]["kind_level"] = "LEVEL A — AUTHORITATIVE (signal/evidence)"
evidence_results[470]["proposed_language"] = "zh"
evidence_results[470]["proposed_kind"] = "signal"
evidence_results[470]["write_eligible"] = False
evidence_results[470]["notes"] = "Language is explicitly 'zh' (Chinese). Not en/ru -> NO_WRITE."

# 3. Post 444: russia-eaeu-ivd-registration-transition-2026-2028
# Title: 2026—2028年俄罗斯与EAEU医疗器械注册过渡期：中国IVD厂家路线图
# Category: [52]
# File: work/agent-handoff/results/FYZ-20260729-CONTENT-BATCH-002/ZH-BIOMED-02 or RU-BIOMED-02
# Stated in 0.3.6.1: "444 等为'俄罗斯主题但中文写作'的歧义文"
evidence_results[444]["language_evidence"].append({
    "file": "work/fyzsxnb-ui-v2/qa/UI2-0361-DEPLOYMENT-REPORT-20260820.md",
    "record": "Section 4: '444 等为 俄罗斯主题但中文写作 的歧义文——是否 RU 需人工确认'"
})
evidence_results[444]["kind_evidence"].append({
    "file": "work/fyzsxnb-ui-v2/qa/UI2-0361-DEPLOYMENT-REPORT-20260820.md",
    "record": "No authoritative kind declaration in frozen manifests (roadmap/guide in title, but no explicit kind meta)"
})
evidence_results[444]["language_level"] = "LEVEL C — CONFLICT (ZH content about RU topic, Cat52, lacks Cat54)"
evidence_results[444]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[444]["proposed_language"] = ""
evidence_results[444]["proposed_kind"] = ""
evidence_results[444]["write_eligible"] = False
evidence_results[444]["notes"] = "Ambiguous Russian topic written in Chinese. Lacks Cat 54. Requires human editorial review. NO_WRITE."

# 4. Post 435: gacc-order-281-special-goods-2026
# File: work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-435/patched-content.seo.json
# Stated: language: "zh", Category [52]
evidence_results[435]["language_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-435/patched-content.seo.json",
    "record": 'Explicit field: "language": "zh"'
})
evidence_results[435]["kind_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-435/patched-content.seo.json",
    "record": 'No authoritative publishing kind (patched-content marked "patch")'
})
evidence_results[435]["language_level"] = "LEVEL A — AUTHORITATIVE (zh)"
evidence_results[435]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[435]["proposed_language"] = "zh"
evidence_results[435]["proposed_kind"] = ""
evidence_results[435]["write_eligible"] = False
evidence_results[435]["notes"] = "Language is 'zh'. Kind lacks original publishing declaration. NO_WRITE."

# 5. Post 424: national-anti-fraud-center-ai-content-identification-guide
# Title: 国家反诈中心AI内容鉴定怎么用？结果能证明什么
# Category: [50] (China Tech)
evidence_results[424]["language_evidence"].append({
    "file": "None in results/ (early tech post)",
    "record": "Chinese language article in Category 50 (China Tech), published before contract"
})
evidence_results[424]["kind_evidence"].append({
    "file": "None in results/",
    "record": "No authoritative metadata record in task manifests"
})
evidence_results[424]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[424]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[424]["proposed_language"] = ""
evidence_results[424]["proposed_kind"] = ""
evidence_results[424]["write_eligible"] = False
evidence_results[424]["notes"] = "Pre-contract post. No authoritative publishing record. NO_WRITE."

# 6. Post 411: china-pharma-exports-2026-formulations-glp1-api
# File: work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-411/patched-content.seo.json
# Stated: language: "zh", Category [52]
evidence_results[411]["language_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-411/patched-content.seo.json",
    "record": 'Explicit field: "language": "zh"'
})
evidence_results[411]["kind_evidence"].append({
    "file": "work/agent-handoff/results/FYZ-20260731-CONTENT-CLUSTER-PATCH-001/post-411/patched-content.seo.json",
    "record": 'No authoritative publishing kind'
})
evidence_results[411]["language_level"] = "LEVEL A — AUTHORITATIVE (zh)"
evidence_results[411]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[411]["proposed_language"] = "zh"
evidence_results[411]["proposed_kind"] = ""
evidence_results[411]["write_eligible"] = False
evidence_results[411]["notes"] = "Language is 'zh'. Kind lacks original publishing declaration. NO_WRITE."

# 7. Post 394: plaud-baseband-engineer-ai-earbuds-signal-analysis
# Title: PLAUD 招聘基带工程师意味着什么：AI 耳机还是独立联网录音设备？
# Category: [50, 55] (China Tech, Hardware)
evidence_results[394]["language_evidence"].append({
    "file": "None in task manifests",
    "record": "Pre-contract China Tech article"
})
evidence_results[394]["kind_evidence"].append({
    "file": "None in task manifests",
    "record": "No authoritative kind record"
})
evidence_results[394]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[394]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[394]["proposed_language"] = ""
evidence_results[394]["proposed_kind"] = ""
evidence_results[394]["write_eligible"] = False
evidence_results[394]["notes"] = "Pre-contract post. NO_WRITE."

# 8. Post 388: shenzhen-biomed-special-items-import-export-process-2026
# Title: 深圳生物医药特殊物品进出口机制：哪些环节真的变快了？
# Category: [52] (China Biomed)
evidence_results[388]["language_evidence"].append({
    "file": "None in task manifests",
    "record": "Pre-contract China Biomed article"
})
evidence_results[388]["kind_evidence"].append({
    "file": "None in task manifests",
    "record": "No authoritative kind record"
})
evidence_results[388]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[388]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[388]["proposed_language"] = ""
evidence_results[388]["proposed_kind"] = ""
evidence_results[388]["write_eligible"] = False
evidence_results[388]["notes"] = "Pre-contract post. NO_WRITE."

# 9. Post 358: waic-2026-agent-phone-robots-product-signal
# Title: WAIC 2026 阶跃展台的信号：智能体手机、汽车与机器人，哪类先落地？
# Category: [50] (China Tech)
evidence_results[358]["language_evidence"].append({
    "file": "None in task manifests",
    "record": "Pre-contract China Tech article"
})
evidence_results[358]["kind_evidence"].append({
    "file": "None in task manifests",
    "record": "No authoritative kind record"
})
evidence_results[358]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[358]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[358]["proposed_language"] = ""
evidence_results[358]["proposed_kind"] = ""
evidence_results[358]["write_eligible"] = False
evidence_results[358]["notes"] = "Pre-contract post. NO_WRITE."

# 10. Post 355: xiaomi-mijia-water-flosser-pro-product-signal
# Title: 小米米家智能冲牙器 Pro 开售：349 元定价背后的产品信号与选购框架
# Category: [50] (China Tech)
evidence_results[355]["language_evidence"].append({
    "file": "None in task manifests",
    "record": "Pre-contract China Tech article"
})
evidence_results[355]["kind_evidence"].append({
    "file": "None in task manifests",
    "record": "No authoritative kind record"
})
evidence_results[355]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[355]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[355]["proposed_language"] = ""
evidence_results[355]["proposed_kind"] = ""
evidence_results[355]["write_eligible"] = False
evidence_results[355]["notes"] = "Pre-contract post. NO_WRITE."

# 11. Post 347: kimi-k3-zhihu-open-source-model
# Title: Kimi K3 为什么刷屏：知乎争议、2.8 万亿参数与开源模型的新问题
# Category: [50] (China Tech)
evidence_results[347]["language_evidence"].append({
    "file": "None in task manifests",
    "record": "Pre-contract China Tech article"
})
evidence_results[347]["kind_evidence"].append({
    "file": "None in task manifests",
    "record": "No authoritative kind record"
})
evidence_results[347]["language_level"] = "LEVEL B — STRONG BUT NOT AUTHORITATIVE (ZH text/title)"
evidence_results[347]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[347]["proposed_language"] = ""
evidence_results[347]["proposed_kind"] = ""
evidence_results[347]["write_eligible"] = False
evidence_results[347]["notes"] = "Pre-contract post. NO_WRITE."

# 12. Post 213: %e8%b7%a8%e5%a2%83%e7%94%b5%e5%95%86%e4%ba%9a%e9%a9%ac%e9%80%8a%e5%be%b7%e5%9b%bdschweberegale...
# Title: 跨境电商亚马逊德国Schweberegale（浮动搁板）市场研究分析报告-20251013
# Category: [33] (Cross-border Ecommerce / Historic CJK)
evidence_results[213]["language_evidence"].append({
    "file": "Historic CJK seed database (2025)",
    "record": "Early 2025 CJK market research report"
})
evidence_results[213]["kind_evidence"].append({
    "file": "None",
    "record": "Legacy archive"
})
evidence_results[213]["language_level"] = "LEVEL D — NO EVIDENCE (Legacy 2025 archive)"
evidence_results[213]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[213]["proposed_language"] = ""
evidence_results[213]["proposed_kind"] = ""
evidence_results[213]["write_eligible"] = False
evidence_results[213]["notes"] = "Legacy 2025 archive. Lacks publication metadata contract. NO_WRITE."

# 13. Post 209: %e8%b7%a8%e5%a2%83%e7%94%b5%e5%95%86%e4%ba%9a%e9%a9%ac%e9%80%8a%e5%b9%b3%e5%8f%b0%e6%b3%95%e5%9b%bd...
# Title: 跨境电商亚马逊平台法国首饰收纳市场深度分析报告-2025年9月
# Category: [33] (Cross-border Ecommerce / Historic CJK)
evidence_results[209]["language_evidence"].append({
    "file": "Historic CJK seed database (2025)",
    "record": "Early 2025 CJK market research report"
})
evidence_results[209]["kind_evidence"].append({
    "file": "None",
    "record": "Legacy archive"
})
evidence_results[209]["language_level"] = "LEVEL D — NO EVIDENCE (Legacy 2025 archive)"
evidence_results[209]["kind_level"] = "LEVEL D — NO EVIDENCE"
evidence_results[209]["proposed_language"] = ""
evidence_results[209]["proposed_kind"] = ""
evidence_results[209]["write_eligible"] = False
evidence_results[209]["notes"] = "Legacy 2025 archive. Lacks publication metadata contract. NO_WRITE."

# Summary metrics
total_unknown = len(TARGET_IDS)
fully_confirmed = sum(1 for r in evidence_results.values() if "LEVEL A" in r["language_level"] and "LEVEL A" in r["kind_level"])
lang_only = sum(1 for r in evidence_results.values() if "LEVEL A" in r["language_level"] and "LEVEL A" not in r["kind_level"])
kind_only = sum(1 for r in evidence_results.values() if "LEVEL A" not in r["language_level"] and "LEVEL A" in r["kind_level"])
conflict_count = sum(1 for r in evidence_results.values() if "CONFLICT" in r["language_level"] or "CONFLICT" in r["kind_level"])
no_ev_count = sum(1 for r in evidence_results.values() if "LEVEL D" in r["language_level"] and "LEVEL D" in r["kind_level"])
write_eligible = sum(1 for r in evidence_results.values() if r["write_eligible"])
no_write = sum(1 for r in evidence_results.values() if not r["write_eligible"])

print("================================================================")
print("      0.4.2 UNKNOWN METADATA RESOLUTION AUDIT SUMMARY           ")
print("================================================================")
print(f"UNKNOWN_TOTAL:               {total_unknown}")
print(f"FULLY_CONFIRMED:             {fully_confirmed} (Posts 479, 470 with language='zh')")
print(f"LANGUAGE_ONLY_CONFIRMED:     {lang_only} (Posts 435, 411 with language='zh')")
print(f"KIND_ONLY_CONFIRMED:         {kind_only}")
print(f"CONFLICT:                    {conflict_count} (Post 444: RU topic in ZH text, Cat52 without Cat54)")
print(f"NO_EVIDENCE:                 {no_ev_count} (Posts 213, 209)")
print(f"RU_STRUCTURE_CONFLICT:       0 (No proposed RU posts)")
print(f"EN_STRUCTURE_CONFLICT:       0 (No proposed EN posts)")
print(f"WRITE_ELIGIBLE:              {write_eligible}")
print(f"NO_WRITE:                    {no_write}")
print("================================================================")

# Dump JSON evidence
out_json_path = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\qa\UNKNOWN-METADATA-EVIDENCE-042.json"
with open(out_json_path, "w", encoding="utf-8") as f:
    json.dump(evidence_results, f, indent=2, ensure_ascii=False)

print(f"Saved evidence JSON to: {out_json_path}")
