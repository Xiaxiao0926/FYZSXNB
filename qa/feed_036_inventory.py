#!/usr/bin/env python
"""feed_036_inventory.py — READ-ONLY inventory for UI V2 0.3.6 Feed Hardening.

Pulls all published posts + category map, re-computes the LEGACY plugin
classification (locale + guide) byte-for-byte to mirror the current homepage
feeds, and emits the migration/audit table consumed by the 0.3.6 report.

No writes of any kind.
"""
from __future__ import annotations
import base64, json, os, re, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

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
    h = {"User-Agent": "fyz-036-inventory/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    if data is not None:
        h["Content-Type"] = "application/json"
    try:
        with urlopen(Request(f"{API}{path}", data=data, headers=h, method=method), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            pages = r.headers.get("X-WP-TotalPages", "1")
            return r.status, (json.loads(txt) if txt else {}), pages
    except HTTPError as e:
        return e.code, (json.loads(e.read().decode("utf-8", "replace")) if e.headers.get("Content-Type", "").startswith("application/json") else e.read().decode("utf-8", "replace")[:300]), "1"


def fetch_posts():
    out, page = [], 1
    while True:
        st, rows, pages = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'publish',
                                '_fields': 'id,slug,title,link,date,categories'})}")
        if st != 200 or not isinstance(rows, list):
            return out, {"http": st, "error": rows}
        out.extend(rows)
        if page >= int(pages):
            break
        page += 1
    return out, None


def fetch_home_blocks(url):
    try:
        with urlopen(Request(url, headers={"User-Agent": "fyz-036-baseline/0.1"}), timeout=45) as r:
            raw = r.read().decode("utf-8", "replace")
    except Exception as e:
        return {"url": url, "error": str(e)}
    def slugs(cls):
        return re.findall(rf'<article class="{cls}[^"]*">.*?<h3><a href="([^"]+)"', raw, re.S)
    return {
        "url": url,
        "signals": [u.rsplit("/", 2)[-2] for u in slugs("fyz-signal")],
        "guides": [u.rsplit("/", 2)[-2] for u in slugs("fyz-guide")],
        "has_feed_marker": "fyzsxnb-home-feed:start" in raw,
    }


def main():
    posts, err = fetch_posts()
    rows = []
    loc_stat = {"ru-RU": 0, "en-US": 0, "unknown": 0}
    kind_stat = {"guide": 0, "signal_or_other": 0}
    for p in posts:
        loc = legacy_locale(p)
        guide = legacy_guide(p, loc) if loc else False
        loc_stat[loc if loc else "unknown"] += 1
        kind_stat["guide" if guide else "signal_or_other"] += 1
        rows.append({
            "id": p["id"], "slug": p["slug"], "date": p.get("date"),
            "title": (p.get("title") or {}).get("rendered") or "",
            "categories": p.get("categories") or [],
            "legacy_locale": loc, "legacy_guide": guide,
            "has_locale_meta": False, "has_kind_meta": False,
        })

    report = {
        "step": "0.3.6 read-only feed inventory (legacy classification)",
        "api_error": err,
        "published_total": len(posts),
        "locale_stats": loc_stat,
        "kind_stats": kind_stat,
        "migration": rows,
        "home_baseline": {
            "en": fetch_home_blocks(SITE + "/"),
            "ru": fetch_home_blocks(SITE + "/ru/"),
        },
    }
    with open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "feed_036_inventory_report.json"), "w",
              encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    print(json.dumps({
        "published_total": len(posts),
        "locale_stats": loc_stat,
        "kind_stats": kind_stat,
        "home_baseline": report["home_baseline"],
        "api_error": err,
    }, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
