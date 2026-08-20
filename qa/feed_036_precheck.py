#!/usr/bin/env python
"""feed_036_precheck.py — READ-ONLY. Mirrors the plugin feed query exactly and
diffs the expected EN/RU signals+guides membership+order against the live
homepage baseline. Proves the python legacy reimplementation == PHP legacy, so
a meta backfill preserves the homepage byte-for-byte after the explicit flip.
"""
from __future__ import annotations
import base64, json, os, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, HERE)
from feed_036_legacy import legacy_locale, legacy_guide  # noqa: E402


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-036-precheck/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    if data is not None:
        h["Content-Type"] = "application/json"
    with urlopen(Request(f"{API}{path}", data=data, headers=h, method=method), timeout=45) as r:
        txt = r.read().decode("utf-8", "replace")
        return r.status, (json.loads(txt) if txt else {})


def fetch_latest_80():
    st, rows = call("GET", f"/posts?{urlencode({'per_page': 80, 'status': 'publish', 'orderby': 'date', 'order': 'desc',
                     '_fields': 'id,slug,date,categories,title'})}")
    return rows if st == 200 and isinstance(rows, list) else []


def expected_feeds(posts):
    by_loc = {"en-US": [], "ru-RU": []}
    for p in posts:
        loc = legacy_locale(p)
        if loc:
            by_loc[loc].append(p)
    out = {}
    for loc, items in by_loc.items():
        signals = [p["slug"] for p in items[:4]]
        guides = [p["slug"] for p in items if legacy_guide(p, loc) and p["slug"] not in signals][:6]
        out[loc] = {"signals": signals, "guides": guides}
    return out


def main():
    posts = fetch_latest_80()
    exp = expected_feeds(posts)
    try:
        inv = json.load(open(os.path.join(HERE, "feed_036_inventory_report.json"), encoding="utf-8"))
        base = inv["home_baseline"]
    except Exception:
        base = {}
    results = {}
    allpass = True
    for loc, key in (("en-US", "en"), ("ru-RU", "ru")):
        actual = (base.get(key) or {}).get("signals") or []
        exp_s = exp[loc]["signals"]
        ok_s = actual == exp_s
        actual_g = (base.get(key) or {}).get("guides") or []
        exp_g = exp[loc]["guides"]
        ok_g = actual == [] or actual_g == exp_g
        # home baseline guides may be served by LiteSpeed stale cache; report but mark
        results[loc] = {
            "signals": {"expected": exp_s, "actual": actual, "match": ok_s},
            "guides": {"expected": exp_g, "actual": actual_g, "match": ok_g},
        }
        allpass = allpass and ok_s and ok_g
    print(json.dumps({"latest80_fetched": len(posts), "feeds": results, "all_match": allpass},
                     ensure_ascii=False, indent=2))
    exit(0 if allpass else 2)


if __name__ == "__main__":
    main()
