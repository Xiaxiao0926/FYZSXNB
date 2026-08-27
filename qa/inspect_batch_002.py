import os, json, re

SEARCH_DIR = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results\FYZ-20260729-CONTENT-BATCH-002"

for root, dirs, files in os.walk(SEARCH_DIR):
    for f in files:
        if f.endswith((".json", ".md")):
            fp = os.path.join(root, f)
            with open(fp, "r", encoding="utf-8", errors="ignore") as fh:
                txt = fh.read()
                if "444" in txt or "russia-eaeu" in txt or "435" in txt or "gacc" in txt:
                    print(f"--- File: {os.path.relpath(fp, SEARCH_DIR)} ---")
                    print(txt[:1000])
