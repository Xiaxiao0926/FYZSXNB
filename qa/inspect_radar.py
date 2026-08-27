import json
import os

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

# Check RADAR_TIER_POOL.json
radar_file = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\RADAR_TIER_POOL.json"
if os.path.exists(radar_file):
    with open(radar_file, "r", encoding="utf-8") as f:
        radar_data = json.load(f)
    print(f"RADAR_TIER_POOL loaded. Type: {type(radar_data)}")
    if isinstance(radar_data, dict):
        items = radar_data.get("items", []) or radar_data.get("records", []) or radar_data.get("articles", [])
        if not items:
            items = list(radar_data.values()) if isinstance(list(radar_data.values())[0], dict) else [radar_data]
    elif isinstance(radar_data, list):
        items = radar_data
    else:
        items = []
    
    print(f"RADAR items count: {len(items)}")
    for it in items:
        if isinstance(it, dict):
            s = json.dumps(it, ensure_ascii=False)
            for pid, slug in SLUGS.items():
                if str(pid) in s or slug in s:
                    print(f"Match for {pid}: {it.get('title') or it.get('slug') or it.get('id')}")
                    print(f"  Details: {it}")
