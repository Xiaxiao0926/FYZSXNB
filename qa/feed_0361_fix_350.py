#!/usr/bin/env python
"""feed_0361_fix_350.py — single-content fix (P2): post 350 kimi-k3-ru-open-model
is Russian content with _fyz_content_language=ru but lacks category 54
(Russian Library) — the language contract requires RU posts to carry cat 54.

Adds category 54, keeps all existing categories; does NOT touch slug, URL,
publish date, content, or canonical. Records before/after.
"""
from __future__ import annotations
import base64, json, os, sys
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
PID = 350
RU_CAT = 54


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-0361-fix350/0.1", "Accept": "application/json"}
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


st, before = call("GET", f"/posts/{PID}?_fields=id,slug,status,date,link,meta,categories")
bm = before.get("meta") or {}
before_cats = before.get("categories") or []
print("BEFORE:", json.dumps({"id": PID, "slug": before.get("slug"), "status": before.get("status"),
                             "date": before.get("date"), "link": before.get("link"),
                             "categories": before_cats,
                             "lang": bm.get("_fyz_content_language"), "kind": bm.get("_fyz_content_kind")},
                            ensure_ascii=False, indent=1))

confirmed_ru = (bm.get("_fyz_content_language") == "ru")
if not confirmed_ru:
    print("NOT fixing: post is not confirmed ru (meta lang:", repr(bm.get("_fyz_content_language")), ")")
    sys.exit(2)
if RU_CAT in before_cats:
    print("Already has cat 54; nothing to do.")
    sys.exit(0)

new_cats = sorted(set(before_cats) | {RU_CAT})
st_p, resp = call("PATCH", f"/posts/{PID}", {"categories": new_cats})
st2, after = call("GET", f"/posts/{PID}?_fields=id,slug,status,date,link,meta,categories")
am = after.get("meta") or {}
print("PATCH http:", st_p)
print("AFTER:", json.dumps({"slug": after.get("slug"), "status": after.get("status"),
                            "date": after.get("date"), "link": after.get("link"),
                            "categories": after.get("categories"),
                            "lang": am.get("_fyz_content_language"), "kind": am.get("_fyz_content_kind")},
                           ensure_ascii=False, indent=1))
ok = (st_p == 200 and RU_CAT in (after.get("categories") or [])
      and after.get("slug") == before.get("slug") and after.get("date") == before.get("date")
      and am.get("_fyz_content_language") == "ru")
print("FIXED:", ok)
sys.exit(0 if ok else 2)
