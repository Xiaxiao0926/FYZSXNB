#!/usr/bin/env python
"""feed_036_accept.py — UI V2 0.3.6 acceptance, scenarios A-F.

A normal feeds            B shortage shrinks (no cross-locale / snapshot / empty)
C publish EN post         D re-type signals<->guides   E delete (no resurrection)
F unknown locale default-off + decision trace

Uses the plugin's QA endpoints (auth-gated) + public homepage fetches.
Creates one temporary EN test post, verifies, then force-deletes it (finally
block guarantees cleanup even on failure). Run AFTER the final deploy + backfill.
"""
from __future__ import annotations
import base64, json, os, re, sys, time
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

HERE = os.path.dirname(os.path.abspath(__file__))
SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
QA = f"{SITE}/wp-json/fyzsxnb/v1"
INV = json.load(open(os.path.join(HERE, "feed_036_inventory_report.json"), encoding="utf-8"))
BASELINE = INV["home_baseline"]
EXPECT = {
    "en-US": {"signals": BASELINE["en"]["signals"], "guides": BASELINE["en"]["guides"]},
    "ru-RU": {"signals": BASELINE["ru"]["signals"], "guides": BASELINE["ru"]["guides"]},
}
UNKNOWN_IDS = [r["id"] for r in INV["migration"] if r["legacy_locale"] == ""]


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None, base=API):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-036-accept/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    if data is not None:
        h["Content-Type"] = "application/json"
    try:
        with urlopen(Request(f"{base}{path}", data=data, headers=h, method=method), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return r.status, (json.loads(txt) if txt else {})
    except HTTPError as e:
        return e.code, (json.loads(e.read().decode("utf-8", "replace")) if e.headers.get("Content-Type", "").startswith("application/json") else e.read().decode("utf-8", "replace")[:300])


def feed_state(exclude=None, limit=0):
    q = {}
    if exclude:
        q["exclude"] = ",".join(str(i) for i in exclude)
    if limit:
        q["limit"] = limit
    st, r = call("GET", "/feed-state" + ("?" + urlencode(q) if q else ""), base=QA)
    return st, r


def trace(ids):
    st, r = call("GET", "/feed-trace?ids=" + ",".join(str(i) for i in ids), base=QA)
    return st, r


def public_slugs(url):
    try:
        with urlopen(Request(url, headers={"User-Agent": "fyz-036-accept/0.1"}), timeout=45) as r:
            raw = r.read().decode("utf-8", "replace")
    except Exception as e:
        return {"signals": [], "guides": [], "error": str(e)}
    def slugs(cls):
        return [u.rsplit("/", 2)[-2] for u in re.findall(rf'<article class="{cls}[^"]*">.*?<h3><a href="([^"]+)"', raw, re.S)]
    return {"signals": slugs("fyz-signal"), "guides": slugs("fyz-guide")}


def purge():
    return call("DELETE", "/feed-cache", base=QA)


def id_slug_map():
    out = {}
    page = 1
    while True:
        st, rows = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'publish', '_fields': 'id,slug'})}")
        if st != 200 or not isinstance(rows, list) or not rows:
            break
        for r in rows:
            out[r["id"]] = r["slug"]
        if len(rows) < 100:
            break
        page += 1
    return out


def main():
    report = {"step": "0.3.6 acceptance A-F", "results": {}, "passed": True}
    test_id, test_slug = None, None
    SLUG = id_slug_map()

    def check(name, ok, detail):
        report["results"][name] = {"pass": bool(ok), "detail": detail}
        if not ok:
            report["passed"] = False

    try:
        # -------- precondition: purge transients, then force a real render of
        #         EN and RU homepages so transients exist; capture RU cache age.
        st, _ = purge()
        check("pre.purge", st == 200, {"http": st})
        time.sleep(1)
        public_slugs(SITE + "/")
        public_slugs(SITE + "/ru/")
        time.sleep(1)
        st0, state0 = feed_state()
        ru_cached_at = {t: (state0.get("ru-RU") or {}).get(t, {}).get("cached_at") for t in ("signals", "guides")}
        ru_cache_ok = st0 == 200 and all(v is not None for v in ru_cached_at.values())
        check("pre.ru_cache_warm", ru_cache_ok, {"ru_cached_at": ru_cached_at, "http": st0})

        # -------- A: normal feeds (fresh render = baseline parity)
        st, state = feed_state()
        ok_a = st == 200
        detail_a = {}
        for loc in ("en-US", "ru-RU"):
            for t in ("signals", "guides"):
                ids = (state.get(loc) or {}).get(t, {}).get("effective_ids", [])
                slugs = [SLUG.get(i, "?") for i in ids]
                detail_a[f"{loc}.{t}"] = {"slugs": slugs, "expected": EXPECT[loc][t]}
                ok_a = ok_a and slugs[: len(EXPECT[loc][t])] == EXPECT[loc][t]
        check("A.normal_feeds", ok_a, detail_a)

        # -------- B: shortage shrink (simulate exclude on the live feed)
        ok_b, detail_b = True, {}
        for loc in ("en-US", "ru-RU"):
            for t in ("signals", "guides"):
                base_ids = (state.get(loc) or {}).get(t, {}).get("effective_ids", [])
                n = len(base_ids)
                k = min(3, max(0, n - 1))
                if k < 1:
                    detail_b[f"{loc}.{t}"] = "skipped: not enough candidates"
                    continue
                st2, sim = feed_state(exclude=base_ids[:k], limit=6 if t == "guides" else 4)
                eff = (sim.get(loc) or {}).get(t, {}).get("effective_ids", [])
                st3, tr = trace(eff)
                in_locale = all(e.get("locale") == loc for e in tr) if isinstance(tr, list) else False
                ok_sim = st2 == 200 and st3 == 200 and len(eff) == n - k and in_locale
                ok_b = ok_b and ok_sim
                detail_b[f"{loc}.{t}"] = {"expected": n - k, "got": len(eff), "in_locale": in_locale}
        check("B.shortage_shrink", ok_b, detail_b)

        # -------- F: unknown locale default-off + trace
        stf, trf = trace(UNKNOWN_IDS)
        ok_f = stf == 200 and all(isinstance(e, dict) and e.get("eligible") is False and e.get("reason") == "locale_unknown" for e in trf)
        all_feed_ids = set()
        for loc in ("en-US", "ru-RU"):
            for t in ("signals", "guides"):
                all_feed_ids.update((state.get(loc) or {}).get(t, {}).get("effective_ids", []))
        ok_f = ok_f and not (all_feed_ids & set(UNKNOWN_IDS))
        check("F.unknown_locale_off", ok_f, {"unknown_ids": UNKNOWN_IDS, "trace": trf, "leaked_into_feeds": sorted(all_feed_ids & set(UNKNOWN_IDS))})

        # -------- C: publish EN test post
        stc, created = call("POST", "/posts", {
            "title": "FYZ 0.3.6 cache acceptance test",
            "status": "publish",
            "categories": [50],
            "content": "<!-- temporary 0.3.6 acceptance post, deleted after the run -->",
            "excerpt": "Temporary acceptance post.",
            "meta": {"_fyz_content_language": "en"},
        })
        ok_c = stc in (200, 201)
        if ok_c:
            test_id, test_slug = created.get("id"), created.get("slug")
            time.sleep(1)
            st_c1, state_c1 = feed_state()
            en_sig = (state_c1.get("en-US") or {}).get("signals", {}).get("effective_ids", [])
            ru_sig = (state_c1.get("ru-RU") or {}).get("signals", {}).get("effective_ids", [])
            cached_en_sig = (state_c1.get("en-US") or {}).get("signals", {}).get("cached")
            ru_cached_at_after = {t: (state_c1.get("ru-RU") or {}).get(t, {}).get("cached_at") for t in ("signals", "guides")}
            en_home = public_slugs(SITE + "/")
            ru_home = public_slugs(SITE + "/ru/")
            ok_c = (test_id in en_sig and test_id not in ru_sig
                    and cached_en_sig is False
                    and ru_cached_at_after == ru_cached_at
                    and test_slug in en_home["signals"] and test_slug not in ru_home["signals"])
            check("C.publish_en", ok_c, {
                "test_id": test_id, "test_slug": test_slug,
                "en_signals_head": [SLUG.get(i, "?") for i in en_sig[:5]],
                "ru_signals_head": [SLUG.get(i, "?") for i in ru_sig[:5]],
                "en_signals_cached_after_publish": cached_en_sig,
                "ru_cached_at_before": ru_cached_at, "ru_cached_at_after": ru_cached_at_after,
                "en_home_has": test_slug in en_home["signals"], "ru_home_has": test_slug in ru_home["signals"],
            })
        else:
            check("C.publish_en", False, {"http": stc, "resp": created})

        # -------- D: re-type EN test post to guide
        if test_id:
            std, upd = call("PATCH", f"/posts/{test_id}", {"meta": {"_fyz_content_kind": "guide"}})
            time.sleep(1)
            st_d1, state_d1 = feed_state()
            en_guides = (state_d1.get("en-US") or {}).get("guides", {}).get("effective_ids", [])
            en_sig = (state_d1.get("en-US") or {}).get("signals", {}).get("effective_ids", [])
            ru_guides = (state_d1.get("ru-RU") or {}).get("guides", {}).get("effective_ids", [])
            ok_d = (std == 200 and test_id in en_guides and en_sig.count(test_id) <= 1 and test_id not in ru_guides)
            check("D.retype_to_guide", ok_d, {
                "http": std,
                "en_guides_head": [SLUG.get(i, "?") for i in en_guides[:5]],
                "en_signals_contains_once": en_sig.count(test_id),
                "ru_guides_contains": test_id in ru_guides,
            })
        else:
            check("D.retype_to_guide", False, "no test post")

        # -------- E: delete test post -> no resurrection
        if test_id:
            ste, _ = call("DELETE", f"/posts/{test_id}?force=true")
            time.sleep(1)
            st_e1, state_e1 = feed_state()
            en_all = [i for t in ("signals", "guides") for i in (state_e1.get("en-US") or {}).get(t, {}).get("effective_ids", [])]
            ru_all = [i for t in ("signals", "guides") for i in (state_e1.get("ru-RU") or {}).get(t, {}).get("effective_ids", [])]
            en_home = public_slugs(SITE + "/")
            time.sleep(2)
            st_e2, state_e2 = feed_state()
            en_all2 = [i for t in ("signals", "guides") for i in (state_e2.get("en-US") or {}).get(t, {}).get("effective_ids", [])]
            ok_e = (ste == 200 and test_id not in en_all and test_id not in en_all2 and test_id not in ru_all
                    and test_slug not in en_home["signals"] and test_slug not in en_home["guides"])
            check("E.delete_no_resurrection", ok_e, {
                "delete_http": ste, "in_en_after": test_id in en_all, "in_en_after_refill": test_id in en_all2,
                "in_ru_after": test_id in ru_all,
                "on_en_home": test_slug in en_home["signals"] or test_slug in en_home["guides"],
            })
            test_id = None
        else:
            check("E.delete_no_resurrection", False, "no test post")

        # -------- X: decision trace shape (guide vs signal sample)
        guide_posts = [r for r in INV["migration"] if r["legacy_guide"] and r["legacy_locale"] == "en-US"]
        signal_posts = [r for r in INV["migration"] if not r["legacy_guide"] and r["legacy_locale"] == "en-US"]
        st_g, tr_g = trace([guide_posts[0]["id"]]) if guide_posts else (0, [])
        st_s, tr_s = trace([signal_posts[0]["id"]]) if signal_posts else (0, [])
        ok_tr = bool(tr_g and tr_g[0].get("eligible") and "guides" in tr_g[0].get("feed_type", [])
                     and tr_g[0].get("reason") == "explicit_locale+kind_guide"
                     and tr_s and tr_s[0].get("eligible") and tr_s[0].get("reason") == "explicit_locale")
        check("X.decision_trace", ok_tr, {"guide_sample": tr_g, "signal_sample": tr_s})
    finally:
        if test_id:
            try:
                call("DELETE", f"/posts/{test_id}?force=true")
                report["cleanup"] = {"deleted": test_id}
            except Exception as e:
                report["cleanup"] = {"error": str(e)}

    report["passed"] = all(v.get("pass") for v in report["results"].values())
    with open(os.path.join(HERE, "feed_036_accept_report.json"), "w", encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    print(json.dumps({"passed": report["passed"],
                      "results": {k: {"pass": v["pass"]} for k, v in report["results"].items()}},
                     ensure_ascii=False, indent=2))
    sys.exit(0 if report["passed"] else 2)


if __name__ == "__main__":
    main()
