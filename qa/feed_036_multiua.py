#!/usr/bin/env python
"""feed_036_multiua.py — READ-ONLY. 5-UA consistency + asset-404 sanity for the
EN/RU homepages after the 0.3.6 deploy. Mirrors the 0.3.5 acceptance approach:
deterministic UAs, cache-busted param variant, structural markers compared.
"""
from __future__ import annotations
import json, re
from urllib.request import Request, urlopen
from urllib.error import HTTPError

SITE = "https://fyzsxnb.com"
UAS = {
    "chrome": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36",
    "mobile": "Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1",
    "googlebot": "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
    "plain": "fyz-036-multiua/0.1",
    "bingbot": "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)",
}
CARD_RE = {
    "signals": re.compile(r'<article class="fyz-signal[^"]*">.*?<h3><a href="([^"]+)"', re.S),
    "guides": re.compile(r'<article class="fyz-guide[^"]*">.*?<h3><a href="([^"]+)"', re.S),
}
ASSET_RE = re.compile(r'(?:src|href)="(https://fyzsxnb\.com/[^"]+\.(?:css|js|woff2?)[^"]*)"')


def fetch(url, ua):
    try:
        with urlopen(Request(url, headers={"User-Agent": ua, "Accept": "text/html"}), timeout=45) as r:
            raw = r.read().decode("utf-8", "replace")
            return r.status, raw, r.headers
    except HTTPError as e:
        return e.code, "", {}
    except Exception as e:
        return 0, "", {"error": str(e)}


def main():
    out = {"pages": {}, "consistent": True}
    per_page = {}
    for key, url in (("en", SITE + "/"), ("ru", SITE + "/ru/")):
        for ua_name, ua in UAS.items():
            for variant in ("clean", "bust"):
                u = url + ("?x036=1" if variant == "bust" else "")
                st, raw, hdr = fetch(u, ua)
                cards = {t: [x.rsplit("/", 2)[-2] for x in CARD_RE[t].findall(raw)] for t in ("signals", "guides")}
                markers = {
                    "h1_count": len(re.findall(r"<h1[\s>]", raw)),
                    "has_nav": "fyz-home" in raw and ("header" in raw.lower()),
                    "has_footer": "site-footer" in raw or "fyz-site-footer" in raw,
                    "has_feed_marker": "fyzsxnb-home-feed:start" in raw,
                }
                sig = (cards["signals"], cards["guides"], markers)
                per_page.setdefault(key, {})[f"{ua_name}_{variant}"] = {"http": st, "signals": cards["signals"], "guides": cards["guides"], "markers": markers, "sig": sig}
                out["pages"][f"{key}_{ua_name}_{variant}"] = {"http": st, "cards": cards, "markers": markers, "cache": hdr.get("X-LiteSpeed-Cache", "?")}
        ref = per_page[key]["chrome_clean"]["sig"]
        mismatches = {k: v["sig"] for k, v in per_page[key].items() if v["sig"] != ref}
        out[f"{key}_consistency"] = {"reference": per_page[key]["chrome_clean"]["sig"], "mismatches": {k: m for k, m in mismatches.items()}}
        out["consistent"] = out["consistent"] and not mismatches

    # asset 404 scan on the reference pages
    assets = {}
    for key in ("en", "ru"):
        st, raw, _ = fetch(SITE + ("/" if key == "en" else "/ru/"), UAS["chrome"])
        urls = sorted(set(ASSET_RE.findall(raw)))
        bad = []
        for u in urls:
            try:
                with urlopen(Request(u, headers={"User-Agent": UAS["chrome"]}), timeout=30) as r:
                    if r.status != 200:
                        bad.append([u, r.status])
            except HTTPError as e:
                bad.append([u, e.code])
            except Exception as e:
                bad.append([u, str(e)])
        assets[key] = {"scanned": len(urls), "bad": bad}
    out["assets"] = assets
    out["consistent"] = out["consistent"] and all(not a["bad"] for a in assets.values())

    print(json.dumps({"consistent": out["consistent"],
                      "en_consistency": out["en_consistency"]["mismatches"],
                      "ru_consistency": out["ru_consistency"]["mismatches"],
                      "assets": assets,
                      "page_http": {k: v["http"] for k, v in out["pages"].items() if v["http"] != 200}},
                     ensure_ascii=False, indent=2))
    with open("feed_036_multiua_report.json", "w", encoding="utf-8") as fh:
        json.dump(out, fh, ensure_ascii=False, indent=2)
    import sys
    sys.exit(0 if out["consistent"] else 2)


if __name__ == "__main__":
    main()
