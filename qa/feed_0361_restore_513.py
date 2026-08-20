#!/usr/bin/env python
"""feed_0361_restore_513.py — restores post 513 (tayron dq381 EN) after the
acceptance F probe wrongly demoted it (pre-v1.2.4 gate fired on a REST edit of
a post that had language but no kind).

Restore: kind='signal' (contract completion) + status=publish + original
excerpt (from fyz_publish_tay03.py cfg) in ONE PATCH so the REST gate sees
complete meta. Date/slug/URL/canonical untouched.
"""
from __future__ import annotations
import base64, json, os, sys
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
PID = 513
ORIGINAL_EXCERPT = ("Chinese Tayron owners report a repeated DQ381 emergency-mode pattern involving "
                    "lost gears, reverse failure and diagnostic codes including P173500, P175E00 and "
                    "P176F00. Here is what the cases actually prove\u2014and what they do not.")


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-0361-restore/0.1", "Accept": "application/json"}
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


st, before = call("GET", f"/posts/{PID}?_fields=id,slug,status,date,meta,excerpt,link")
print("BEFORE:", json.dumps({k: before.get(k) for k in ("id", "slug", "status", "date", "link")}, ensure_ascii=False))
print("BEFORE meta:", json.dumps(before.get("meta"), ensure_ascii=False))
print("BEFORE excerpt:", repr(((before.get("excerpt") or {}).get("rendered") or "")[:80]))

st_p, resp = call("PATCH", f"/posts/{PID}", {
    "status": "publish",
    "excerpt": ORIGINAL_EXCERPT,
    "meta": {"_fyz_content_language": "en", "_fyz_content_kind": "signal"},
})
st_g, after = call("GET", f"/posts/{PID}?_fields=id,slug,status,date,meta,excerpt,link")
print("PATCH http:", st_p)
print("AFTER:", json.dumps({k: after.get(k) for k in ("id", "slug", "status", "date", "link")}, ensure_ascii=False))
print("AFTER meta:", json.dumps(after.get("meta"), ensure_ascii=False))
print("AFTER excerpt ok:", ((after.get("excerpt") or {}).get("rendered") or "").strip() == ORIGINAL_EXCERPT.strip())
ok = (st_p == 200 and after.get("status") == "publish"
      and (after.get("meta") or {}).get("_fyz_content_language") == "en"
      and (after.get("meta") or {}).get("_fyz_content_kind") == "signal"
      and after.get("slug") == before.get("slug") and after.get("date") == before.get("date"))
print("RESTORED:", ok)
sys.exit(0 if ok else 2)
