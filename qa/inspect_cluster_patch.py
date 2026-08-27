import json, os

BASE = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results\FYZ-20260731-CONTENT-CLUSTER-PATCH-001"

for p in ["batch-manifest.json", "RESULT.md", "post-411/patched-content.seo.json", "post-435/patched-content.seo.json"]:
    fp = os.path.join(BASE, p)
    if os.path.exists(fp):
        print(f"--- File: {p} ---")
        with open(fp, "r", encoding="utf-8") as f:
            print(f.read()[:800])
