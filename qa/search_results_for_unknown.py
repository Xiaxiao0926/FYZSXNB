import json
import os
import re

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

RESULTS_DIR = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results"

matches = {pid: [] for pid in TARGET_IDS}

for root, dirs, files in os.walk(RESULTS_DIR):
    for f in files:
        if f.endswith((".json", ".md")):
            fp = os.path.join(root, f)
            try:
                with open(fp, "r", encoding="utf-8", errors="ignore") as fh:
                    txt = fh.read()
                    for pid in TARGET_IDS:
                        slug_hint = SLUGS[pid]
                        if re.search(r"\b" + str(pid) + r"\b", txt) or slug_hint in txt.lower():
                            matches[pid].append((os.path.relpath(fp, RESULTS_DIR), f))
            except Exception:
                pass

print("=== Results Directory Search for 13 Unknown Posts ===")
for pid, hits in matches.items():
    print(f"\n--- Post {pid} (Hits: {len(hits)}) ---")
    for rel_path, fname in hits[:10]:
        print(f"  {rel_path}")
