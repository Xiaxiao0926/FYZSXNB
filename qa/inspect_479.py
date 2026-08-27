import json, os

path_479_seo = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results\FYZ-20260807-PUBLISH10-BIOMED-A-001\manual-biomed-udi-ivd-2027\nmpa-udi-2027-class2-devices-ivd-implementation-guide.seo.json"
path_479_pub = r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\results\FYZ-20260810-BIOMED-A-REVISION-004\publication\apply.json"

for p in [path_479_seo, path_479_pub]:
    if os.path.exists(p):
        print(f"--- {p} ---")
        with open(p, "r", encoding="utf-8") as f:
            print(f.read()[:500])
