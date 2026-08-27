import json

TARGET_IDS = [479, 470, 444, 435, 424, 411, 394, 388, 358, 355, 347, 213, 209]

with open(r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\agent-handoff\PENDING_TASKS.json", "r", encoding="utf-8") as f:
    pt = json.load(f)

tasks = pt.get("tasks", []) if isinstance(pt, dict) else pt
print(f"Total tasks in PENDING_TASKS.json: {len(tasks)}")

for t in tasks:
    t_str = json.dumps(t, ensure_ascii=False)
    for pid in TARGET_IDS:
        if str(pid) in t_str:
            print(f"\n--- Post {pid} found in task: {t.get('task_id')} ---")
            print(f"  Title: {t.get('title')}")
            print(f"  Status: {t.get('status')}")
            print(f"  Target language: {t.get('target_language') or t.get('language') or t.get('locale')}")
            print(f"  Content kind / Tier: {t.get('tier') or t.get('kind') or t.get('article_type')}")
            print(f"  Keys in task: {list(t.keys())}")
            # print all key values
            for k, v in t.items():
                if isinstance(v, (str, int, float, bool)):
                    print(f"    {k}: {v}")
