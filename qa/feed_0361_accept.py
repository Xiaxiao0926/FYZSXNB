#!/usr/bin/env python
"""feed_0361_accept.py — 0.3.6.1 Publication Metadata Contract acceptance.

A: EN signal publish (explicit meta) -> published, eligible in EN feeds, RU unchanged
B: RU guide  publish (explicit meta) -> published, eligible in RU guides, EN unchanged
C: publish without language          -> demoted to pending (no half-published state)
D: publish without kind              -> demoted to pending
E: REST draft with both meta         -> written + read back identical
F: editing an existing post (meta untouched) -> metadata not lost/overwritten
+ parity re-check: EN/RU feed candidates unchanged vs 0.3.6 baseline.
All created test posts are force-deleted in the finally block.
"""
from __future__ import annotations
import base64, json, os, sys, time
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


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None, base=API):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-0361-accept/0.1", "Accept": "application/json"}
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


def id_slug_map():
    out, page = {}, 1
    while True:
        st, rows = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'any', '_fields': 'id,slug'})}")
        if st != 200 or not isinstance(rows, list) or not rows:
            break
        for r in rows:
            out[r["id"]] = r["slug"]
        if len(rows) < 100:
            break
        page += 1
    return out


def main():
    report = {"step": "0.3.6.1 Publication Metadata Contract acceptance", "results": {}, "passed": True}
    created = []

    def check(name, ok, detail):
        report["results"][name] = {"pass": bool(ok), "detail": detail}
        if not ok:
            report["passed"] = False

    try:
        SLUG = id_slug_map()

        # ---------- P: feed parity (run FIRST, before any test post exists) —
        #         proves the v1.2.4 deploy changed no feed behaviour vs 0.3.6.
        st0, state0 = feed_state()
        ok_p = True
        detail_p = {}
        for loc in ("en-US", "ru-RU"):
            sig_raw = (state0.get(loc) or {}).get("signals", {}).get("effective_ids", [])
            sig_rendered = [SLUG.get(i, "?") for i in sig_raw[:4]]
            ok_p = ok_p and sig_rendered == EXPECT[loc]["signals"]
            detail_p[f"{loc}.signals"] = {"slugs_head": sig_rendered, "expected": EXPECT[loc]["signals"]}
            st_g, state_g = feed_state(exclude=sig_raw[:4], limit=6)
            g_rendered = [SLUG.get(i, "?") for i in (state_g.get(loc) or {}).get("guides", {}).get("effective_ids", [])]
            ok_p = ok_p and st_g == 200 and g_rendered == EXPECT[loc]["guides"]
            detail_p[f"{loc}.guides"] = {"slugs_head": g_rendered, "expected": EXPECT[loc]["guides"]}
        check("P.feed_parity_unchanged", ok_p, detail_p)

        # baseline candidates (before any test post)
        base_ids = {}
        for loc in ("en-US", "ru-RU"):
            base_ids[loc] = {}
            for t in ("signals", "guides"):
                base_ids[loc][t] = set((state0.get(loc) or {}).get(t, {}).get("effective_ids", []))

        # ---------- A: EN signal publish with explicit meta
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 A EN signal test", "status": "publish", "categories": [50],
            "content": "<!-- temporary -->", "excerpt": "temporary",
            "meta": {"_fyz_content_language": "en", "_fyz_content_kind": "signal"},
        })
        ok_a = st in (200, 201)
        if ok_a:
            pid = post.get("id")
            created.append(pid)
            st_g, got = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            time.sleep(1)
            st_s, state_s = feed_state()
            en_sig = (state_s.get("en-US") or {}).get("signals", {}).get("effective_ids", [])
            ru_ids = set()
            for t in ("signals", "guides"):
                ru_ids |= set((state_s.get("ru-RU") or {}).get(t, {}).get("effective_ids", []))
            ok_a = (got.get("status") == "publish"
                    and meta.get("_fyz_content_language") == "en" and meta.get("_fyz_content_kind") == "signal"
                    and pid in en_sig and pid not in ru_ids
                    and base_ids["ru-RU"]["signals"] | base_ids["ru-RU"]["guides"] == ru_ids)
            check("A.en_signal_publish", ok_a, {
                "id": pid, "status": got.get("status"), "meta": meta,
                "in_en_signals": pid in en_sig, "in_ru": pid in ru_ids,
                "ru_unchanged": base_ids["ru-RU"]["signals"] | base_ids["ru-RU"]["guides"] == ru_ids,
            })
        else:
            check("A.en_signal_publish", False, {"http": st, "resp": post})

        # ---------- B: RU guide publish with explicit meta
        # capture EN rendered state right after A (before B) for the
        # "RU publish must not touch EN" assertion
        st_b0, state_b0 = feed_state()
        en_b0_sig = (state_b0.get("en-US") or {}).get("signals", {}).get("effective_ids", [])
        st_bg, state_bg = feed_state(exclude=en_b0_sig[:4], limit=6)
        en_before_b = {
            "signals": en_b0_sig[:4],
            "guides": (state_bg.get("en-US") or {}).get("guides", {}).get("effective_ids", []),
        }
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 B RU guide test", "status": "publish", "categories": [50, 54],
            "content": "<!-- temporary -->", "excerpt": "temporary",
            "meta": {"_fyz_content_language": "ru", "_fyz_content_kind": "guide"},
        })
        ok_b = st in (200, 201)
        if ok_b:
            pid = post.get("id")
            created.append(pid)
            st_g, got = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            time.sleep(1)
            st_s, state_s = feed_state()
            ru_guides = (state_s.get("ru-RU") or {}).get("guides", {}).get("effective_ids", [])
            en_sig_raw = (state_s.get("en-US") or {}).get("signals", {}).get("effective_ids", [])
            st_eg, state_eg = feed_state(exclude=en_sig_raw[:4], limit=6)
            # EN must be bit-identical to its state right after A (before B):
            # a RU publish must not touch EN feeds at all.
            en_after = {"signals": en_sig_raw[:4], "guides": (state_eg.get("en-US") or {}).get("guides", {}).get("effective_ids", [])}
            ok_b = (got.get("status") == "publish"
                    and meta.get("_fyz_content_language") == "ru" and meta.get("_fyz_content_kind") == "guide"
                    and pid in ru_guides and pid not in en_sig_raw
                    and en_after == en_before_b)
            check("B.ru_guide_publish", ok_b, {
                "id": pid, "status": got.get("status"), "meta": meta,
                "in_ru_guides_candidates": pid in ru_guides, "in_en": pid in en_sig_raw,
                "en_before_b": en_before_b, "en_after_b": en_after,
            })
        else:
            check("B.ru_guide_publish", False, {"http": st, "resp": post})

        # ---------- C: publish without language -> demoted to pending
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 C missing language", "status": "publish", "categories": [50],
            "content": "<!-- temporary -->", "excerpt": "temporary",
            "meta": {"_fyz_content_kind": "signal"},
        })
        if st in (200, 201):
            pid = post.get("id")
            created.append(pid)
            st_g, got = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            time.sleep(1)
            st_s, state_s = feed_state()
            leaked = any(pid in (state_s.get(loc) or {}).get(t, {}).get("effective_ids", [])
                         for loc in ("en-US", "ru-RU") for t in ("signals", "guides"))
            ok_c = (got.get("status") == "pending" and meta.get("_fyz_content_language") in (None, "")
                    and not leaked)
            check("C.missing_language_blocked", ok_c, {
                "id": pid, "final_status": got.get("status"), "meta": meta, "leaked_into_feed": leaked,
            })
        else:
            check("C.missing_language_blocked", False, {"http": st, "resp": post})

        # ---------- D: publish without kind -> demoted to pending
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 D missing kind", "status": "publish", "categories": [50],
            "content": "<!-- temporary -->", "excerpt": "temporary",
            "meta": {"_fyz_content_language": "en"},
        })
        if st in (200, 201):
            pid = post.get("id")
            created.append(pid)
            st_g, got = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            time.sleep(1)
            st_s, state_s = feed_state()
            leaked = any(pid in (state_s.get(loc) or {}).get(t, {}).get("effective_ids", [])
                         for loc in ("en-US", "ru-RU") for t in ("signals", "guides"))
            ok_d = (got.get("status") == "pending" and meta.get("_fyz_content_kind") in (None, "")
                    and not leaked)
            check("D.missing_kind_blocked", ok_d, {
                "id": pid, "final_status": got.get("status"), "meta": meta, "leaked_into_feed": leaked,
            })
        else:
            check("D.missing_kind_blocked", False, {"http": st, "resp": post})

        # ---------- E: REST draft with both meta -> read back identical
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 E draft meta roundtrip", "status": "draft", "categories": [50],
            "content": "<!-- temporary -->",
            "meta": {"_fyz_content_language": "en", "_fyz_content_kind": "guide"},
        })
        if st in (200, 201):
            pid = post.get("id")
            created.append(pid)
            st_g, got = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            ok_e = (got.get("status") == "draft" and meta.get("_fyz_content_language") == "en"
                    and meta.get("_fyz_content_kind") == "guide")
            check("E.draft_meta_roundtrip", ok_e, {"id": pid, "status": got.get("status"), "meta": meta})
        else:
            check("E.draft_meta_roundtrip", False, {"http": st, "resp": post})

        # ---------- F: editing must not lose/overwrite metadata
        # F1: PATCH a draft (no meta in body) -> meta intact; PATCH same meta -> intact
        st, post = call("POST", "/posts", {
            "title": "FYZ 0.3.6.1 F metadata preservation", "status": "draft", "categories": [50],
            "content": "<!-- temporary -->",
            "meta": {"_fyz_content_language": "en", "_fyz_content_kind": "signal"},
        })
        ok_f = st in (200, 201)
        if ok_f:
            pid = post.get("id")
            created.append(pid)
            st_p1, _ = call("POST", f"/posts/{pid}", {"title": "FYZ 0.3.6.1 F metadata preservation (edited)"})
            st_g1, g1 = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            m1 = (g1.get("meta") or {}) if isinstance(g1, dict) else {}
            st_p2, _ = call("POST", f"/posts/{pid}", {"meta": {"_fyz_content_language": "en", "_fyz_content_kind": "signal"}})
            st_g2, g2 = call("GET", f"/posts/{pid}?_fields=id,status,meta")
            m2 = (g2.get("meta") or {}) if isinstance(g2, dict) else {}
            ok_f = (st_p1 == 200 and st_p2 == 200
                    and m1.get("_fyz_content_language") == "en" and m1.get("_fyz_content_kind") == "signal"
                    and m2.get("_fyz_content_language") == "en" and m2.get("_fyz_content_kind") == "signal")
            check("F1.edit_preserves_meta", ok_f, {"id": pid, "after_plain_edit": m1, "after_meta_edit": m2})
        else:
            check("F1.edit_preserves_meta", False, {"http": st, "resp": post})
        # F2: read-only check on an existing published post (metadata not regressed)
        target = None
        for r in INV["migration"]:
            if r["legacy_locale"] == "en-US" and r["id"] in SLUG:
                target = r["id"]
                break
        if target:
            st_g, got = call("GET", f"/posts/{target}?_fields=id,status,meta")
            meta = (got.get("meta") or {}) if isinstance(got, dict) else {}
            ok_f2 = (got.get("status") == "publish"
                     and meta.get("_fyz_content_language") in ("en", "ru")
                     and meta.get("_fyz_content_kind") in ("signal", "guide"))
            check("F2.existing_meta_complete", ok_f2, {"id": target, "status": got.get("status"), "meta": meta})
        else:
            check("F2.existing_meta_complete", False, "no eligible existing post found")
    finally:
        for pid in created:
            try:
                call("DELETE", f"/posts/{pid}?force=true")
            except Exception:
                pass
        report["cleanup_deleted"] = created

    report["passed"] = all(v.get("pass") for v in report["results"].values())
    with open(os.path.join(HERE, "feed_0361_accept_report.json"), "w", encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    print(json.dumps({"passed": report["passed"],
                      "results": {k: {"pass": v["pass"]} for k, v in report["results"].items()}},
                     ensure_ascii=False, indent=2))
    sys.exit(0 if report["passed"] else 2)


if __name__ == "__main__":
    main()
