#!/usr/bin/env python
"""accept_035.py — 0.3.5 homepage migration acceptance (deterministic UA, raw fetch).
DOM contract, hardcoded-URL, SEO, RU parity, feeds locale, footer."""
from __future__ import annotations
import io, re, urllib.request, json, os

UA = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126 Safari/537.36", "Cache-Control": "no-cache"}

def fetch(url):
    with urllib.request.urlopen(urllib.request.Request(url + "?x=035a", headers=UA), timeout=45) as r:
        return r.status, r.read().decode("utf-8", "replace")

out = {}

# 1) EN home
st, en = fetch("https://fyzsxnb.com/")
def section_count(html, cls):
    return len(re.findall(r'class="[^"]*\b' + re.escape(cls) + r"\b", html))
def class_count(html, cls):
    m = re.findall(r'class="([^"]*)"', html)
    c = 0
    for x in m:
        if re.search(r'(?:^|\s)' + re.escape(cls) + r'(?:\s|$)', x):
            c += 1
    return c

out["en"] = {
    "http": st,
    "h1_hero": bool(re.search(r'<h1[^>]*id="fyz-home-title"[^>]*>\s*What China is building', en)),
    "h1_total": 1 if (class_count(en, "cfc-h1") == 0) else None,  # placeholder
    "nav_chinatech": bool(re.search(r">China Tech<", en)),
    "signals": class_count(en, "fyz-signal"),
    "featured": class_count(en, "fyz-feature-lead") + class_count(en, "fyz-feature-small") + class_count(en, "fyz-compact"),
    "desks": class_count(en, "fyz-desk"),
    "guides": class_count(en, "fyz-guide"),
    "trust": "fyz-trust" in en,
    "cta": "fyz-cta-band" in en,
    "reading": class_count(en, "fyz-reading-card"),
    "no_legacy_wp_html": "<!-- wp:html -->" not in en,
    "footer_unified": bool(re.search(r"\u00a9 2026 FYZSXNB", en)) and "fyz-site-footer" not in en,
    "powered0": not re.search(r"Powered by WordPress", en, re.I),
    "canonical_self": bool(re.search(r'rel="canonical"[^>]*href="https://fyzsxnb\.com/"', en)),
    "hreflang": bool(re.search(r'hreflang="ru"', en)),
    "lang": re.search(r'<html[^>]*lang="([^"]+)"', en).group(1) if re.search(r'<html[^>]*lang="([^"]+)"', en) else None,
    "og": re.search(r'property="og:locale"[^>]*content="([^"]+)"', en).group(1) if re.search(r'property="og:locale"[^>]*content="([^"]+)"', en) else None,
}
# h1 count on home = hero only + no other h1: count all <h1>
out["en"]["h1_total"] = len(re.findall(r"<h1\b", en))

# 2) RU home
st, ru = fetch("https://fyzsxnb.com/ru/")
out["ru"] = {
    "http": st,
    "lang": re.search(r'<html[^>]*lang="([^"]+)"', ru).group(1) if re.search(r'<html[^>]*lang="([^"]+)"', ru) else None,
    "h1_hero_ru": bool(re.search(r'<h1[^>]*id="fyz-home-title"[^>]*>\s*Китайские технологии и товары', ru)),
    "signals": class_count(ru, "fyz-signal"),
    "desks": class_count(ru, "fyz-desk"),
    "guides": class_count(ru, "fyz-guide"),
    "featured_suppressed": 'fyz-featured' not in ru,
    "reading_suppressed": 'fyz-reading-card' not in ru,
    "trust_method": "fyz-method" in ru or "Как мы проверяем сведения" in ru,
    "footer_unified": bool(re.search(r"\u00a9 2026 FYZSXNB", ru)),
    "powered0": not re.search(r"Powered by WordPress", ru, re.I),
    "canonical_self_ru": bool(re.search(r'rel="canonical"[^>]*href="https://fyzsxnb\.com/ru/"', ru)),
    "no_legacy_wp_html": "<!-- wp:html -->" not in ru,
}
# RU signals must be RU-only (no EN slugs), EN signals EN-only (no RU slugs)
ru_slugs = ["volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay", "dq381-avariynyy-rezhim", "byd-frigate-07", "kak-proverit-byd", "bambu-lab", "davleniya"]
en_slugs = ["china-market-volkswagen-tayron", "dq381-emergency-mode-owner-cases", "fda-foreign-drug", "glp1-generic", "ai-voice-recorder", "fully-automated-molecular"]
out["ru"]["en_leak"] = any(s in ru for s in en_slugs)
out["en"]["ru_leak"] = any(s in en for s in ru_slugs)

# 3) hardcoded-absolute internal URLs inside homepage SOURCE (config/templates) — must be 0
source_dirs = [
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child\inc\home.php",
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child\front-page.php",
    r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child\page.php",
] + [os.path.join(r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child\template-parts\home", f)
     for f in os.listdir(r"C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child\template-parts\home")]
hardcoded = []
for p in source_dirs:
    t = io.open(p, encoding="utf-8").read()
    found = re.findall(r"https://fyzsxnb\.com/[^\"' ]+", t)
    hardcoded.extend(found)
out["hardcoded_absolute_in_source"] = hardcoded

# 4) SEO regression on key pages
seo = {}
for name, u, expect in [("home-en", "https://fyzsxnb.com/", "en"), ("home-ru", "https://fyzsxnb.com/ru/", "ru"), ("article", "https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/", "en")]:
    st, h = fetch(u)
    cn = re.search(r'rel="canonical"[^>]*href="([^"]+)"', h)
    seo[name] = {"http": st, "canonical": cn.group(1) if cn else None, "h1": len(re.findall(r"<h1\b", h)),
                 "robots": re.search(r'name="robots"[^>]*content="([^"]*)"', h).group(1) if re.search(r'name="robots"[^>]*content="([^"]*)"', h) else None}
out["seo"] = seo

print(json.dumps(out, ensure_ascii=False, indent=2))
ok = (
    out["en"]["http"] == 200 and out["en"]["h1_hero"] and out["en"]["h1_total"] == 1
    and out["en"]["signals"] >= 2 and out["en"]["featured"] >= 4 and out["en"]["desks"] >= 2
    and out["en"]["guides"] >= 2 and out["en"]["trust"] and out["en"]["cta"]
    and out["en"]["no_legacy_wp_html"] and out["en"]["footer_unified"] and out["en"]["powered0"]
    and out["en"]["canonical_self"] and out["en"]["hreflang"]
    and out["ru"]["http"] == 200 and out["ru"]["h1_hero_ru"] and out["ru"]["signals"] >= 2
    and out["ru"]["guides"] >= 2 and out["ru"]["featured_suppressed"] and out["ru"]["reading_suppressed"]
    and out["ru"]["footer_unified"] and out["ru"]["powered0"] and out["ru"]["canonical_self_ru"]
    and out["ru"]["en_leak"] is False and out["en"]["ru_leak"] is False
    and not out["hardcoded_absolute_in_source"]
    and all(seo[k]["http"] == 200 and seo[k]["h1"] == 1 for k in seo)
)
print("VERDICT:", "PASS" if ok else "FAIL")