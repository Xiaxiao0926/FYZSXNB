import json

with open("work/fyzsxnb-ui-v2/qa/LOCALE-PRODUCTION-META-SNAPSHOT-041.json", "r", encoding="utf-8") as f:
    posts = json.load(f)

# Also load feed_036_inventory_report.json to get titles
titles = {}
try:
    with open("work/fyzsxnb-ui-v2/qa/feed_036_inventory_report.json", "r", encoding="utf-8") as f:
        inv = json.load(f)
        for item in inv.get("migration", []):
            titles[item["id"]] = item.get("title", "")
except Exception:
    pass

unknown_posts = [p for p in posts if not p.get("content_language")]
print(f"Total unknown posts: {len(unknown_posts)}")
for p in unknown_posts:
    pid = p["post_id"]
    print(f"ID: {pid:<5} Slug: {p['slug']:<60} Cats: {str(p['categories']):<12} Kind: '{p.get('content_kind', '')}' Title: '{titles.get(pid, '')}'")
