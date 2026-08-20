#!/usr/bin/env python
"""feed_036_probe.py — READ-ONLY. Probes two RU candidates (status/date/cats) and
re-fetches the live RU homepage guides to distinguish stale-cache vs status issues.
"""
from __future__ import annotations
import base64, json, os, re
from urllib.parse import urlencode
from urllib.request import Request, urlopen

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path):
    h = {"User-Agent": "fyz-036-probe/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    with urlopen(Request(f"{API}{path}", headers=h, method=method), timeout=45) as r:
        txt = r.read().decode("utf-8", "replace")
        return r.status, (json.loads(txt) if txt else {})


out = {}
for slug in ("proverka-epts-po-vin-pered-pokupkoj", "ru-ifind-tbr-evidence-russia-laboratory-guide"):
    st, rows = call("GET", f"/posts?{urlencode({'slug': slug, 'status': 'any',
                    '_fields': 'id,slug,status,date,modified,categories,title'})}")
    out[slug] = {"http": st, "rows": rows if isinstance(rows, list) else rows}

try:
    with urlopen(Request(SITE + "/ru/", headers={"User-Agent": "fyz-036-probe/0.1", "Cache-Control": "no-cache"}), timeout=45) as r:
        raw = r.read().decode("utf-8", "replace")
    guides = re.findall(r'<article class="fyz-guide[^"]*">.*?<h3><a href="([^"]+)"', raw, re.S)
    out["live_ru_guides"] = [u.rsplit("/", 2)[-2] for u in guides]
    out["live_ru_has_cache_headers"] = dict(r.headers)
except Exception as e:
    out["live_ru_guides"] = {"error": str(e)}

print(json.dumps(out, ensure_ascii=False, indent=2, default=str))
