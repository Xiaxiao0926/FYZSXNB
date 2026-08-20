#!/usr/bin/env python
"""feed_036_probe2.py — READ-ONLY. Fetches / and /ru/ under several UA + query
variants to determine whether the RU guides "mismatch" is a LiteSpeed cache
variant (bot UA) rather than plugin logic.
"""
from __future__ import annotations
import json, re
from urllib.request import Request, urlopen

SITE = "https://fyzsxnb.com"
UAS = {
    "chrome": ("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
               "Chrome/126.0.0.0 Safari/537.36"),
    "plain": "fyz-036-probe/0.1",
    "googlebot": "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
}
GUIDE_RE = re.compile(r'<article class="fyz-guide[^"]*">.*?<h3><a href="([^"]+)"', re.S)
SIG_RE = re.compile(r'<article class="fyz-signal[^"]*">.*?<h3><a href="([^"]+)"', re.S)
EXPECT_RU_GUIDES = [
    "kak-proverit-byd-pered-ustanovkoy-openpilot-camera-can-ecu-fingerprint",
    "chery-android-auto-obnovlenie-tiggo-7-8-pro",
    "proverka-epts-po-vin-pered-pokupkoj",
    "bambu-lab-china-russia-pre-purchase-check",
    "check-chinese-ivd-russia-registration-registry",
    "starter-carburetor-chinese-brushcutter-43-52cc",
]


def fetch(url, ua):
    try:
        with urlopen(Request(url, headers={"User-Agent": ua, "Accept": "text/html"}), timeout=45) as r:
            raw = r.read().decode("utf-8", "replace")
            guides = [u.rsplit("/", 2)[-2] for u in GUIDE_RE.findall(raw)]
            sigs = [u.rsplit("/", 2)[-2] for u in SIG_RE.findall(raw)]
            return {"http": r.status, "cache_header": r.headers.get("X-LiteSpeed-Cache", "?"),
                    "signals": sigs, "guides": guides,
                    "has_proverka": any("proverka-epts" in g for g in guides)}
    except Exception as e:
        return {"error": str(e)}


out = {}
for ua_key, ua in UAS.items():
    out[f"ru_{ua_key}"] = fetch(SITE + "/ru/", ua)
    out[f"en_{ua_key}"] = fetch(SITE + "/", ua)
out["ru_chrome_bust_1"] = fetch(SITE + "/ru/?x036=1", UAS["chrome"])
out["ru_chrome_bust_2"] = fetch(SITE + "/ru/?x036=2", UAS["chrome"])
out["expected_ru_guides"] = EXPECT_RU_GUIDES
print(json.dumps(out, ensure_ascii=False, indent=2))
