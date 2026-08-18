#!/usr/bin/env python
"""cars_from_china_seo_regression.py — READ-ONLY SEO regression checks.

Confirms that the Cars from China scaffolding did NOT:
  - publish empty pages (hub is draft; nothing under /cars-from-china/ is public)
  - break canonical / robots / single H1 / lang on existing key pages
  - leak the draft hub into the public sitemap
Also verifies the draft hub page exists in REST with status=draft.
"""
from __future__ import annotations
import base64, json, os, re
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"

def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}

def get_json(path, auth_hdrs=None):
    h = {"User-Agent": "fyz-seo-regression/0.1", "Accept": "application/json"}
    if auth_hdrs:
        h.update(auth_hdrs)
    try:
        with urlopen(Request(f"{API}{path}", headers=h), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return json.loads(txt) if txt else []
    except HTTPError as e:
        return {"http_error": e.code, "body": e.read().decode("utf-8", "replace")[:300]}

def fetch(url):
    h = {"User-Agent": "fyz-seo-regression/0.1", "Cache-Control": "no-cache"}
    try:
        with urlopen(Request(url, headers=h), timeout=45) as r:
            return r.status, r.read().decode("utf-8", "replace")
    except HTTPError as e:
        return e.code, ""

report = {"check": "FYZSXNB Cars from China — SEO regression (no production changes made by scaffold)"}

# 1) Draft hub is NOT public and NOT in sitemap.
code, _ = fetch(f"{SITE}/cars-from-china/")
report["public_hub_status"] = code  # 404 expected (page is draft)
code_ru, _ = fetch(f"{SITE}/ru/cars-from-china/")
report["public_ru_hub_status"] = code_ru  # 404 expected (rewrite not deployed / no public content)

# sitemap probe
try:
    sm, sitemap = fetch(f"{SITE}/sitemap.xml")
    report["sitemap_http"] = sm
    report["cars_from_china_in_sitemap"] = "cars-from-china" in sitemap
except Exception as e:
    report["sitemap_error"] = str(e)

# 2) Draft page exists in REST with status=draft.
ah = auth()
rows = get_json(f"/pages?{urlencode({'slug': 'cars-from-china', 'status': 'any', '_fields': 'id,slug,status'})}", ah)
if isinstance(rows, list) and rows:
    report["hub_draft"] = {"id": rows[0]["id"], "slug": rows[0]["slug"], "status": rows[0]["status"]}
    report["hub_draft_is_not_published"] = rows[0]["status"] != "publish"
else:
    report["hub_draft"] = rows

# 3) Existing key pages still healthy (representative sample incl. BYD cluster).
probe = [
    "https://fyzsxnb.com/openpilot-byd-2026-support-open-source/",
    "https://fyzsxnb.com/kak-proverit-byd-pered-ustanovkoy-openpilot-camera-can-ecu-fingerprint/",
    "https://fyzsxnb.com/byd-frigate-07-openpilot-dannye-dlya-adaptacii/",
    "https://fyzsxnb.com/ru/",
]
sample = {}
for u in probe:
    code, html = fetch(u)
    entry = {"http": code}
    if code == 200:
        lg = re.search(r'<html[^>]*lang="([^"]+)"', html)
        entry["lang"] = lg.group(1) if lg else None
        cn = re.search(r'rel="canonical"[^>]*href="([^"]+)"', html)
        entry["canonical_ok"] = bool(cn and cn.group(1).rstrip("/") == u.rstrip("/"))
        rb = re.search(r'name="robots"[^>]*content="([^"]+)"', html)
        entry["robots"] = rb.group(1) if rb else None
        entry["h1_count"] = len(re.findall(r"<h1\b", html))
    sample[u.split("/")[-2]] = entry
report["existing_pages"] = sample

print(json.dumps(report, ensure_ascii=False, indent=2))
