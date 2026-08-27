import json, os

path_470 = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results\FYZ-20260807-PUBLISH10-BIOMED-A-001\manual-biomed-crp-saa-poct-stewardship\crp-saa-poct-antibiotic-stewardship-village-clinics.seo.json"
if os.path.exists(path_470):
    with open(path_470, "r", encoding="utf-8") as f:
        print(f.read()[:500])
