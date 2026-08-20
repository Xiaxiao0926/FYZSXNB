#!/usr/bin/env python
"""feed_0361_backfill_kind.py — completes the 0.3.6.1 contract: every published
post with an explicit language but no kind gets kind='signal' (only the 37
legacy guides got kind='guide' during 0.3.6). 'signal' is the default bucket
in the feed logic, so homepage output is unchanged.

Usage: python feed_0361_backfill_kind.py            # dry-run
       python feed_0361_backfill_kind.py --apply    # write via REST
"""
from __future__ import annotations
import base64, json, os, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

HERE = os.path.dirname(os.path.abspath(__file__))
SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
APPLY = "--apply" in sys.argv


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-0361-backfill-kind/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    if data is not None:
        h["Content-Type"] = "application/json"
    try:
        with urlopen(Request(f"{API}{path}", data=data, headers=h, method=method), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return r.status, (json.loads(txt) if txt else {})
    except HTTPError as e:
        return e.code, (json.loads(e.read().decode("utf-8", "replace")) if e.headers.get("Content-Type", "").startswith("application/json") else e.read().decode("utf-8", "replace")[:300])


def fetch_published():
    out, page = [], 1
    while True:
        st, rows = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'publish', '_fields': 'id,slug,status,meta'})}")
        if st != 200 or not isinstance(rows, list):
            return out
        out.extend(rows)
        if len(rows) < 100:
            break
        page += 1
    return out


def main():
    posts = fetch_published()
    plan = []
    for p in posts:
        meta = p.get("meta") or {}
        lang = str((meta.get("_fyz_content_language") or "") or "").strip().lower()
        kind = str((meta.get("_fyz_content_kind") or "") or "").strip().lower()
        if lang in ("en", "ru") and kind == "":
            plan.append({"id": p["id"], "slug": p["slug"], "language": lang, "want_kind": "signal"})
    summary = {"mode": "apply" if APPLY else "dry-run", "published": len(posts), "to_fill": len(plan)}
    if APPLY:
        ok, fail = 0, []
        for row in plan:
            st, resp = call("PATCH", f"/posts/{row['id']}", {"meta": {"_fyz_content_kind": "signal"}})
            if st == 200 and (resp.get("meta") or {}).get("_fyz_content_kind") == "signal":
                ok += 1
            else:
                fail.append({"id": row["id"], "http": st, "resp": resp})
        summary["applied"] = ok
        summary["failed"] = fail
        print(json.dumps(summary, ensure_ascii=False, indent=2))
        sys.exit(0 if not fail else 2)
    else:
        summary["sample"] = plan[:5]
        print(json.dumps(summary, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
