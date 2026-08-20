#!/usr/bin/env python
"""feed_0361_probe.py — debug 0.3.6.1: post dates/status + feed order."""
from __future__ import annotations
import base64, json, os
from urllib.parse import urlencode
from urllib.request import Request, urlopen

SITE = "https://fyzsxnb.com"
API = f"{SITE}/wp-json/wp/v2"


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path):
    h = {"User-Agent": "fyz-0361-probe/0.1", "Accept": "application/json"}
    h.update(auth())
    with urlopen(Request(f"{API}{path}", headers=h, method=method), timeout=45) as r:
        return json.loads(r.read().decode("utf-8", "replace"))


out = {}
rows = call("GET", f"/posts?{urlencode({'per_page': 100, 'status': 'any', '_fields': 'id,slug,status,date,modified'})}")
out["top_newest"] = [{"id": r["id"], "slug": r["slug"], "status": r["status"], "date": r["date"], "modified": r["modified"]}
                     for r in rows[:8]]
state = call("GET", "/wp-json/fyzsxnb/v1/feed-state".replace("/wp-json/fyzsxnb/v1", "/fyzsxnb/v1")) if False else None
h2 = {"User-Agent": "fyz-0361-probe/0.1", "Accept": "application/json"}
h2.update(auth())
with urlopen(Request(f"{SITE}/wp-json/fyzsxnb/v1/feed-state", headers=h2), timeout=45) as r:
    state = json.loads(r.read().decode("utf-8", "replace"))
out["en_signals_ids"] = state["en-US"]["signals"]["effective_ids"][:8]
out["ru_signals_ids"] = state["ru-RU"]["signals"]["effective_ids"][:8]
print(json.dumps(out, ensure_ascii=False, indent=1))
