#!/usr/bin/env python
"""feed_0361_final_check.py — final cleanliness + parity sanity after 0.3.6.1."""
from __future__ import annotations
import base64, json, os, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen

SITE = "https://fyzsxnb.com"
API = f"{SITE}/wp-json/wp/v2"
QA = f"{SITE}/wp-json/fyzsxnb/v1"


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path):
    h = {"User-Agent": "fyz-0361-final/0.1", "Accept": "application/json"}
    h.update(auth())
    with urlopen(Request(f"{API}{path}", headers=h, method=method), timeout=45) as r:
        return json.loads(r.read().decode("utf-8", "replace"))


def qa(method, path):
    h = {"User-Agent": "fyz-0361-final/0.1", "Accept": "application/json"}
    h.update(auth())
    with urlopen(Request(f"{QA}{path}", headers=h, method=method), timeout=45) as r:
        return json.loads(r.read().decode("utf-8", "replace"))


out = {}
posts, page = [], 1
while True:
    rows = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'any', '_fields': 'id,slug,status'})}")
    posts.extend(rows)
    if len(rows) < 100:
        break
    page += 1
out["leftover_test_posts"] = [p for p in posts if "fyz-0-3-6" in (p.get("slug") or "")]
out["total_any_status"] = len(posts)
out["published_count"] = sum(1 for p in posts if p.get("status") == "publish")
out["pending_count"] = sum(1 for p in posts if p.get("status") == "pending")
out["pending_slugs"] = [p["slug"] for p in posts if p.get("status") == "pending"]

# parity sanity: rendered EN/RU feeds vs baseline
inv = json.load(open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "feed_036_inventory_report.json"), encoding="utf-8"))
exp = {"en-US": {"signals": inv["home_baseline"]["en"]["signals"], "guides": inv["home_baseline"]["en"]["guides"]},
       "ru-RU": {"signals": inv["home_baseline"]["ru"]["signals"], "guides": inv["home_baseline"]["ru"]["guides"]}}
state = qa("GET", "/feed-state")
by_id = {p["id"]: p["slug"] for p in posts}
ok = True
for loc in ("en-US", "ru-RU"):
    sig_ids = (state.get(loc) or {}).get("signals", {}).get("effective_ids", [])[:4]
    sig = [by_id.get(i, "?") for i in sig_ids]
    st_g = qa("GET", "/feed-state?" + urlencode({"exclude": ",".join(str(i) for i in sig_ids), "limit": 6}))
    g = [by_id.get(i, "?") for i in (st_g.get(loc) or {}).get("guides", {}).get("effective_ids", [])]
    ok = ok and sig == exp[loc]["signals"] and g == exp[loc]["guides"]
    out[f"{loc}.rendered"] = {"signals": sig, "guides": g}
out["parity_ok"] = ok
print(json.dumps(out, ensure_ascii=False, indent=1))
sys.exit(0 if ok and not out["leftover_test_posts"] else 2)
