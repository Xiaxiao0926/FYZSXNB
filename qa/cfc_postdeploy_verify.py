#!/usr/bin/env python
"""cfc_postdeploy_verify.py — public + SEO verification after CFC theme deploy."""
from __future__ import annotations
import hashlib, json, os, re
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")

def fetch(url):
    try:
        with urlopen(Request(url, headers={"User-Agent": "fyz-cfc-verify/0.1", "Cache-Control": "no-cache"}), timeout=45) as r:
            return r.status, r.read()
    except HTTPError as e:
        return e.code, b""
    except Exception as e:
        return 0, str(e).encode()

# Expected local sha256 for cars-from-china.css (from DEPLOYMENT_MANIFEST.json)
EXPECT_CSS_SHA = "1264cf7fdaede7e1c9552ebc8cb31d4d09a0cb95330795fa042c76f9fee3e493"

report = {"step": "post-deploy public + SEO verification"}

# 1) CSS asset live + hash matches manifest
st, body = fetch(f"{SITE}/wp-content/themes/fyzsxnb-neve-child/assets/css/cars-from-china.css")
report["cars_from_china_css"] = {"http": st}
if st == 200:
    h = hashlib.sha256(body).hexdigest()
    report["cars_from_china_css"]["sha256"] = h
    report["cars_from_china_css"]["matches_manifest"] = h == EXPECT_CSS_SHA
else:
    report["cars_from_china_css"]["upload_pending"] = True

# 2) Entry points (hub is draft -> 404 expected; ru hub/brand need rewrite flush)
for name, path in [("en_hub", "/cars-from-china/"), ("ru_hub", "/ru/cars-from-china/"),
                   ("brand_vw", "/cars-from-china/volkswagen/"), ("model_tayron", "/cars-from-china/volkswagen/tayron/")]:
    c, _ = fetch(SITE + path)
    report[name] = {"http": c, "note": "404 = draft expected (hub) or rewrites not flushed yet"}

# 3) Sitemap exclusion
try:
    sm, sitemap = fetch(f"{SITE}/sitemap.xml")
    report["sitemap"] = {"http": sm, "cars_from_china_in_sitemap": "cars-from-china" in sitemap.decode("utf-8", "replace")}
except Exception as e:
    report["sitemap"] = {"error": str(e)}

# 4) Fatal-error sweep + SEO spot checks on key pages
probes = [
    "https://fyzsxnb.com/",
    "https://fyzsxnb.com/ru/",
    "https://fyzsxnb.com/openpilot-byd-2026-support-open-source/",
    "https://fyzsxnb.com/kak-proverit-byd-pered-ustanovkoy-openpilot-camera-can-ecu-fingerprint/",
    "https://fyzsxnb.com/byd-frigate-07-openpilot-dannye-dlya-adaptacii/",
]
fatal_pat = re.compile(r"Fatal error|Parse error|Warning: |Deprecated: ", re.I)
page_checks = {}
for u in probes:
    c, html = fetch(u)
    txt = html.decode("utf-8", "replace")
    entry = {"http": c}
    fatal = fatal_pat.search(txt)
    entry["fatal_marker"] = bool(fatal)
    if fatal:
        entry["fatal_sample"] = fatal.group(0)[:80]
    if c == 200:
        lg = re.search(r'<html[^>]*lang="([^"]+)"', txt)
        entry["lang"] = lg.group(1) if lg else None
        cn = re.search(r'rel="canonical"[^>]*href="([^"]+)"', txt)
        entry["canonical_ok"] = bool(cn and cn.group(1).rstrip("/") == u.rstrip("/"))
        entry["h1_count"] = len(re.findall(r"<h1\b", txt))
    page_checks[u.split("/")[-2]] = entry
report["existing_pages"] = page_checks
report["all_expected_ok"] = (
    report["cars_from_china_css"].get("matches_manifest") is True
    and report["en_hub"]["http"] == 404
    and report["sitemap"]["cars_from_china_in_sitemap"] is False
    and not any(e.get("fatal_marker") for e in page_checks.values())
    and all(e.get("canonical_ok") and e.get("lang") == "ru-RU" and e.get("h1_count") == 1 for e in page_checks.values() if e.get("http") == 200)
)
print(json.dumps(report, ensure_ascii=False, indent=2))

import sys
sys.exit(0 if report["all_expected_ok"] else 2)