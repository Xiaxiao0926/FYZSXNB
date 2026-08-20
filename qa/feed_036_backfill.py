#!/usr/bin/env python
"""feed_036_backfill.py — 0.3.6 migration: backfill explicit feed meta.

Writes ONLY:
  _fyz_content_language = 'en' | 'ru'   for posts whose legacy classification
                                        is confident (ru: category 54 and/or
                                        Cyrillic; en: non-CJK Latin titles).
  _fyz_content_kind     = 'guide'       for posts the legacy guide decision
                                        classified as guides.

Never writes: categories, taxonomy, content, status. Posts with unknown locale
(CJK / ambiguous) are left WITHOUT meta on purpose: they stay off homepage
feeds and land in the editorial audit list (report table 4).

Usage:
  python feed_036_backfill.py            # dry-run: prints plan, writes plan json
  python feed_036_backfill.py --apply    # performs the writes via REST
"""
from __future__ import annotations
import base64, json, os, sys
from urllib.parse import urlencode
from urllib.request import Request, urlopen
from urllib.error import HTTPError

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, HERE)
from feed_036_legacy import legacy_locale, legacy_guide  # noqa: E402

SITE = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API = f"{SITE}/wp-json/wp/v2"
APPLY = "--apply" in sys.argv


def auth():
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    return {"Authorization": f"Basic {base64.b64encode(f'{u}:{p}'.encode()).decode()}"} if (u and p) else {}


def call(method, path, payload=None):
    data = json.dumps(payload).encode() if payload is not None else None
    h = {"User-Agent": "fyz-036-backfill/0.1", "Accept": "application/json"}
    if auth():
        h.update(auth())
    if data is not None:
        h["Content-Type"] = "application/json"
    try:
        with urlopen(Request(f"{API}{path}", data=data, headers=h, method=method), timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            return r.status, (json.loads(txt) if txt else {})
    except HTTPError as e:
        return e.code, (json.loads(e.read().decode("utf-8", "replace")) if e.headers.get("Content-Type", "").startswith("application/json") else e.read().decode("utf-8", "replace")[:300])


def fetch_all_posts():
    out, page = [], 1
    while True:
        st, rows, = call("GET", f"/posts?{urlencode({'per_page': 100, 'page': page, 'status': 'publish',
                          '_fields': 'id,slug,title,categories,meta'})}")
        if st != 200 or not isinstance(rows, list):
            return out, {"http": st, "error": rows}
        out.extend(rows)
        if len(rows) < 100:
            break
        page += 1
    return out, None


def plan_rows(posts):
    rows = []
    for p in posts:
        meta = p.get("meta") or {}
        cur_lang = str((meta.get("_fyz_content_language") or "") or "").strip().lower()
        cur_kind = str((meta.get("_fyz_content_kind") or "") or "").strip().lower()
        loc = legacy_locale(p)
        guide = legacy_guide(p, loc) if loc else False
        want_lang = {"ru-RU": "ru", "en-US": "en"}.get(loc, "")
        want_kind = "guide" if guide else ""
        rows.append({
            "id": p["id"], "slug": p["slug"],
            "legacy_locale": loc, "legacy_guide": guide,
            "want_language": want_lang, "want_kind": want_kind,
            "cur_language": cur_lang, "cur_kind": cur_kind,
            "needs_language": cur_lang != want_lang,
            "needs_kind": cur_kind != want_kind,
            "needs_write": (cur_lang != want_lang) or (cur_kind != want_kind),
            "unknown": loc == "",
        })
    return rows


def main():
    posts, err = fetch_all_posts()
    rows = plan_rows(posts)
    to_write = [r for r in rows if r["needs_write"]]
    unknown = [r for r in rows if r["unknown"]]
    summary = {
        "mode": "apply" if APPLY else "dry-run",
        "api_error": err,
        "published_total": len(posts),
        "needs_write": len(to_write),
        "language_writes": sum(1 for r in to_write if r["needs_language"]),
        "kind_writes": sum(1 for r in to_write if r["needs_kind"]),
        "already_correct": len(rows) - len(to_write) - len(unknown),
        "unknown_untouched": len(unknown),
        "plan": to_write,
        "unknown_ids": [r["id"] for r in unknown],
    }
    plan_path = os.path.join(HERE, "feed_036_backfill_plan.json")
    with open(plan_path, "w", encoding="utf-8") as fh:
        json.dump(summary, fh, ensure_ascii=False, indent=2)

    if APPLY:
        ok, fail = 0, []
        for r in to_write:
            meta = {}
            if r["needs_language"]:
                meta["_fyz_content_language"] = r["want_language"]
            if r["needs_kind"]:
                meta["_fyz_content_kind"] = r["want_kind"]
            st, resp = call("PATCH", f"/posts/{r['id']}", {"meta": meta})
            if st == 200:
                ok += 1
                r["patched"] = True
            else:
                fail.append({"id": r["id"], "slug": r["slug"], "http": st, "resp": resp})
                r["patched"] = False
        summary["applied"] = ok
        summary["failed"] = fail
        with open(plan_path, "w", encoding="utf-8") as fh:
            json.dump(summary, fh, ensure_ascii=False, indent=2)
        print(json.dumps({"mode": "apply", "applied": ok, "failed": fail, "summary": {
            "published_total": len(posts), "needs_write": len(to_write),
            "language_writes": summary["language_writes"], "kind_writes": summary["kind_writes"],
            "unknown_untouched": len(unknown)}}, ensure_ascii=False, indent=2))
        sys.exit(0 if not fail else 2)
    else:
        print(json.dumps({"mode": "dry-run",
                          "published_total": len(posts),
                          "needs_write": len(to_write),
                          "language_writes": summary["language_writes"],
                          "kind_writes": summary["kind_writes"],
                          "already_correct": summary["already_correct"],
                          "unknown_untouched": len(unknown),
                          "sample_plan": to_write[:5]}, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
