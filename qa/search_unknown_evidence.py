import os
import re
import json

TARGET_IDS = [479, 470, 444, 435, 424, 411, 394, 388, 358, 355, 347, 213, 209]
SLUGS = {
    479: "nmpa-udi-2027",
    470: "crp-saa-poct",
    444: "russia-eaeu-ivd",
    435: "gacc-order-281",
    424: "national-anti-fraud",
    411: "china-pharma-exports",
    394: "plaud-baseband",
    388: "shenzhen-biomed",
    358: "waic-2026",
    355: "xiaomi-mijia",
    347: "kimi-k3",
    213: "schweberegale",
    209: "20251013",
}

SEARCH_ROOT = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work"

results = {pid: [] for pid in TARGET_IDS}

for root, dirs, files in os.walk(SEARCH_ROOT):
    for f in files:
        if f.endswith((".pyc", ".git", ".clixml", ".png", ".jpg")):
            continue
        filepath = os.path.join(root, f)
        try:
            with open(filepath, "r", encoding="utf-8", errors="ignore") as fh:
                content = fh.read()
                for pid in TARGET_IDS:
                    slug_hint = SLUGS[pid]
                    # Check for exact ID reference or slug keyword
                    matches = []
                    if re.search(r"\b" + str(pid) + r"\b", content):
                        matches.append("id_match")
                    if slug_hint in content.lower():
                        matches.append("slug_match")
                    if matches:
                        results[pid].append({
                            "file": os.path.relpath(filepath, SEARCH_ROOT),
                            "match_types": matches,
                        })
        except Exception:
            pass

print("=== Search Results for 13 Unknown Posts ===")
for pid, hits in results.items():
    print(f"\n--- Post {pid} (Slug hint: {SLUGS[pid]}) ---")
    print(f"Total hits: {len(hits)}")
    for h in hits[:10]: # show first 10
        print(f"  [{','.join(h['match_types'])}] {h['file']}")
