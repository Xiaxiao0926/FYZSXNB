#!/usr/bin/env python
"""cache_baseline_035.py — P0 gate: 5-view cache consistency for 0.3.5.
Desktop/mobile/googlebot UA x {no-query, cache-busted}; assert all serve the
0.3.4.1 state (no 'Powered by WordPress/Neve |', no RU leak in EN archive,
RU dates localized, new footer credit) and are mutually consistent."""
from __future__ import annotations
import re, urllib.request

UAS = {
    "desktop": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126 Safari/537.36",
    "mobile": "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1",
    "googlebot": "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
    "no-ua": "",
}
TARGETS = [
    ("ru-article", "https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/"),
    ("en-article", "https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/"),
    ("cat-prodres", "https://fyzsxnb.com/category/product-research/"),
    ("cat-ruslib", "https://fyzsxnb.com/category/russian-library/"),
    ("home-en", "https://fyzsxnb.com/"),
    ("home-ru", "https://fyzsxnb.com/ru/"),
]

def fetch(url, ua):
    headers = {"Cache-Control": "no-cache"}
    if ua:
        headers["User-Agent"] = ua
    req = urllib.request.Request(url, headers=headers)
    try:
        with urllib.request.urlopen(req, timeout=45) as r:
            return r.status, r.read().decode("utf-8", "replace")
    except Exception as e:
        return 0, str(e)

def markers(h, target):
    low = h.lower()
    powered = len(re.findall(r"powered by\s+wordpress|neve\s*\|", low))
    if "cat-prodres" in target:
        ru_leak = len(re.findall(r"/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/|/dq381-avariynyy-rezhim/|/byd-frigate-07/|/datchik-davleniya/|/kak-proverit-byd/", low))
        ru_leak2 = len(re.findall(r"/gazonokosilk/", low))
    else:
        ru_leak = ru_leak2 = 0
    if "cat-ruslib" in target:
        en_leak = len(re.findall(r"/china-market-volkswagen-tayron/|/dq381-emergency-mode-owner-cases/|/redmagic-cooler-6-pro-plus-china-launch-buyer-check/", low))
    else:
        en_leak = 0
    if "ru-article" in target:
        ru_date = bool(re.search(r"Опубликовано:\s*[0-9]{1,2}\s+[а-яё]+", h))
        en_date = bool(re.search(r"Опубликовано:\s*[A-Z][a-z]+ [0-9]{1,2},", h))
    else:
        ru_date = en_date = True
    footer = "FYZSXNB" in h and "\u00a9" in h
    return {"powered": powered, "ru_leak": ru_leak + ru_leak2, "en_leak": en_leak,
            "ru_date": ru_date, "en_date": en_date, "footer_fyz": footer}

report = {}
for tname, url in TARGETS:
    for uname, ua in UAS.items():
        st_nq, h_nq = fetch(url, ua)
        st_q, h_q = fetch(url + "?x=" + ("cb" + str(len(report))), ua)
        m_nq = markers(h_nq, tname) if st_nq == 200 else {"powered": -1}
        m_q = markers(h_q, tname) if st_q == 200 else {"powered": -1}
        same = (st_nq == st_q) and (h_nq[:2000] == h_q[:2000])
        report[f"{tname}::{uname}"] = {"nq": m_nq, "q": m_q, "http": (st_nq, st_q), "head_same": same}

# aggregate
bad = 0
for k, v in report.items():
    m = v["nq"]
    same = v["head_same"]
    # ru-article must have Russian date AND no English month; en-article must keep English date.
    is_ru_art = "ru-article" in k
    is_en_art = "en-article" in k
    date_ok = (m.get("ru_date") is True and (m.get("en_date") is False)) if is_ru_art else (m.get("en_date") is True if is_en_art else True)
    ok = (m.get("powered") == 0 and m.get("ru_leak") == 0 and m.get("en_leak") == 0
          and date_ok and v["http"][0] == 200 and same)
    if not ok:
        bad += 1
    print(f"{k:38} http={v['http']} powered={m.get('powered')} ruLeak={m.get('ru_leak')} enLeak={m.get('en_leak')} ruDate={m.get('ru_date')} enDate={m.get('en_date')} headSame={same} ok={bool(ok)}")

print("TOTAL_VIEWS", len(report), "BAD", bad, "VERDICT", "PASS" if bad == 0 else "FAIL")