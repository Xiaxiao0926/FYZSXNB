import json
import os
import sys

TARGET_IDS = [479, 470, 444, 435, 424, 411, 394, 388, 358, 355, 347, 213, 209]
SLUGS = {
    479: "nmpa-udi-2027-class2-devices-ivd-implementation-guide",
    470: "crp-saa-poct-antibiotic-stewardship-village-clinics",
    444: "russia-eaeu-ivd-registration-transition-2026-2028",
    435: "gacc-order-281-special-goods-2026",
    424: "national-anti-fraud-center-ai-content-identification-guide",
    411: "china-pharma-exports-2026-formulations-glp1-api",
    394: "plaud-baseband-engineer-ai-earbuds-signal-analysis",
    388: "shenzhen-biomed-special-items-import-export-process-2026",
    358: "waic-2026-agent-phone-robots-product-signal",
    355: "xiaomi-mijia-water-flosser-pro-product-signal",
    347: "kimi-k3-zhihu-open-source-model",
    213: "schweberegale",
    209: "20251013",
}

# 1. Check RADAR candidates
radar_file = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\RADAR_TIER_POOL.json"
if os.path.exists(radar_file):
    with open(radar_file, "r", encoding="utf-8") as f:
        radar_data = json.load(f)
    candidates = radar_data.get("candidates", [])
    print(f"Total RADAR candidates: {len(candidates)}")
    for cand in candidates:
        wp = cand.get("wp_post_id")
        slug = cand.get("slug") or cand.get("final_slug") or ""
        lang = cand.get("language") or cand.get("target_language") or cand.get("locale")
        kind = cand.get("content_kind") or cand.get("kind") or cand.get("tier") or cand.get("article_type")
        for pid, s in SLUGS.items():
            if wp == pid or (slug and s in slug):
                print(f"RADAR Candidate Match for Post {pid}:")
                print(f"  Title: {cand.get('title')}")
                print(f"  Slug: {slug}")
                print(f"  WP ID: {wp}")
                print(f"  Language fields: lang={lang}, cand.get('language')={cand.get('language')}")
                print(f"  Kind fields: kind={kind}, type={cand.get('type')}, tier={cand.get('tier')}")
                print(f"  Keys in cand: {list(cand.keys())}")
