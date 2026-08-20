#!/usr/bin/env python
"""feed_036_warm_probe.py — READ-ONLY debug v2: isolate LiteSpeed variant purge
vs render-path issues for the RU homepage after the QA purge."""
from __future__ import annotations
import base64, json, os, time
from urllib.request import Request, urlopen

SITE = "https://fyzsxnb.com"
UA_CHROME = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36"


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def get(url, ua):
    with urlopen(Request(url, headers={"User-Agent": ua, "Accept": "text/html"}), timeout=45) as r:
        return r.status, r.read().decode("utf-8", "replace"), dict(r.headers)


def qa(method, path):
    h = {"User-Agent": UA_CHROME, "Accept": "application/json"}
    h.update(auth())
    with urlopen(Request(SITE + "/wp-json/fyzsxnb/v1" + path, headers=h, method=method), timeout=45) as r:
        return r.status, json.loads(r.read().decode("utf-8", "replace"))


def state():
    st, s = qa("GET", "/feed-state")
    ru = (s or {}).get("ru-RU") or {}
    return {t: ru.get(t, {}).get("cached") for t in ("signals", "guides")}


out = {}
st, body = qa("DELETE", "/feed-cache")
out["purge_http"] = st
time.sleep(4)
out["ru_cached_after_wait"] = state()

# same UA as before -> may hit the old variant
st, raw1, hdr1 = get(SITE + "/ru/", UA_CHROME)
out["chrome_fetch"] = {"http": st, "lite": hdr1.get("X-LiteSpeed-Cache", "?"), "etag": hdr1.get("Etag", "?")}
out["ru_cached_after_chrome"] = state()

# brand-new UA -> must MISS and render
fresh_ua = "fyz-036-warm-" + str(int(time.time()))
st, raw2, hdr2 = get(SITE + "/ru/", fresh_ua)
out["fresh_ua_fetch"] = {"http": st, "lite": hdr2.get("X-LiteSpeed-Cache", "?"), "etag": hdr2.get("Etag", "?")}
out["ru_cached_after_fresh_ua"] = state()

# query param variant under chrome UA
st, raw3, hdr3 = get(SITE + "/ru/?cb=" + str(int(time.time())), UA_CHROME)
out["query_fetch"] = {"http": st, "lite": hdr3.get("X-LiteSpeed-Cache", "?"), "etag": hdr3.get("Etag", "?")}
out["ru_cached_after_query"] = state()

import re
def cards(raw):
    g = re.compile(r'<article class="fyz-guide[^"]*">.*?<h3><a href="([^"]+)"', re.S)
    s = re.compile(r'<article class="fyz-signal[^"]*">.*?<h3><a href="([^"]+)"', re.S)
    return {"signals": [u.rsplit("/", 2)[-2] for u in s.findall(raw)], "guides": [u.rsplit("/", 2)[-2] for u in g.findall(raw)]}

out["chrome_cards"] = cards(raw1)
out["fresh_ua_cards"] = cards(raw2)
out["query_cards"] = cards(raw3)
out["chrome_vs_fresh_identical"] = out["chrome_cards"] == out["fresh_ua_cards"]
print(json.dumps(out, ensure_ascii=False, indent=2))
