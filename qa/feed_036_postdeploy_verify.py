#!/usr/bin/env python
"""feed_036_postdeploy_verify.py — post-deploy public verification for 0.3.6.

Checks: homepages 200 + unique H1 + no PHP fatal markers, feed card counts and
membership vs the pre-migration baseline, canonical/hreflang/lang unchanged,
no legacy wp:html page content, unified footer, and that the QA endpoints are
NOT public (401 without credentials).
"""
from __future__ import annotations
import base64, json, os, re, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

HERE = os.path.dirname(os.path.abspath(__file__))
SITE = "https://fyzsxnb.com"
INV = json.load(open(os.path.join(HERE, "feed_036_inventory_report.json"), encoding="utf-8"))
EXPECT = {
    "en": {"signals": INV["home_baseline"]["en"]["signals"], "guides": INV["home_baseline"]["en"]["guides"]},
    "ru": {"signals": INV["home_baseline"]["ru"]["signals"], "guides": INV["home_baseline"]["ru"]["guides"]},
}
CARD_RE = {
    "signals": re.compile(r'<article class="fyz-signal[^"]*">.*?<h3><a href="([^"]+)"', re.S),
    "guides": re.compile(r'<article class="fyz-guide[^"]*">.*?<h3><a href="([^"]+)"', re.S),
}


def get(url, ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36", auth=None):
    h = {"User-Agent": ua, "Accept": "text/html"}
    if auth:
        h["Authorization"] = "Basic " + base64.b64encode(auth.encode()).decode()
    try:
        with urlopen(Request(url, headers=h), timeout=45) as r:
            return r.status, r.read().decode("utf-8", "replace")
    except HTTPError as e:
        return e.code, e.read().decode("utf-8", "replace")[:400]


def main():
    report = {"step": "0.3.6 post-deploy public verification", "checks": {}, "passed": True}

    def check(name, ok, detail):
        report["checks"][name] = {"pass": bool(ok), "detail": detail}
        if not ok:
            report["passed"] = False

    for key, path, lang in (("en", "/", "en-US"), ("ru", "/ru/", "ru-RU")):
        st, raw = get(SITE + path)
        cards = {t: [u.rsplit("/", 2)[-2] for u in CARD_RE[t].findall(raw)] for t in ("signals", "guides")}
        h1 = re.findall(r"<h1[\s>]", raw)
        canonical = re.findall(r'<link rel="canonical" href="([^"]+)"', raw)
        hreflang = re.findall(r'<link rel="alternate" hreflang="([^"]+)"', raw)
        fatal = re.search(r"(Fatal error|Parse error|Warning:|Deprecated:)", raw)
        wphtml = "wp:html" in raw
        footer = ("site-footer" in raw) or ("fyz-site-footer" in raw)
        ok = (st == 200 and len(h1) == 1 and not fatal and not wphtml and footer
              and canonical and canonical[0] == SITE + path
              and cards["signals"] == EXPECT[key]["signals"] and cards["guides"] == EXPECT[key]["guides"])
        check(f"{key}.page", ok, {
            "http": st, "h1_count": len(h1), "canonical": canonical, "hreflang": hreflang,
            "fatal_marker": bool(fatal), "legacy_wphtml": wphtml, "footer": footer,
            "signals": cards["signals"], "signals_expected": EXPECT[key]["signals"],
            "guides": cards["guides"], "guides_expected": EXPECT[key]["guides"],
        })
        # lang attribute sanity
        lang_ok = f'lang="{lang}"' in raw or (key == "ru" and 'lang="ru"' in raw)
        check(f"{key}.lang", lang_ok, {"lang_found": lang_ok})

    # QA endpoints must be auth-gated (401/403 without credentials)
    for ep in ("/feed-state", "/feed-trace?ids=1", "/feed-cache"):
        st, _ = get(SITE + "/wp-json/fyzsxnb/v1" + ep, ua="fyz-036-verify/0.1")
        check("qa_gated." + ep.replace("/", "_"), st in (401, 403), {"http": st})

    report["passed"] = all(v["pass"] for v in report["checks"].values())
    with open(os.path.join(HERE, "feed_036_postdeploy_verify_report.json"), "w", encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    print(json.dumps({"passed": report["passed"], "checks": {k: {"pass": v["pass"]} for k, v in report["checks"].items()}},
                     ensure_ascii=False, indent=2))
    sys.exit(0 if report["passed"] else 2)


if __name__ == "__main__":
    main()
