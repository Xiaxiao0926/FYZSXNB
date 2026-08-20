#!/usr/bin/env python3
"""fetch_production_truth_041.py — READ-ONLY production metadata fetcher for 0.4.1.

Fetches all published posts with context=edit to observe actual meta._fyz_content_language
and meta._fyz_content_kind directly from production WordPress REST API.
"""
from __future__ import annotations
import base64
import json
import os
import sys
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from urllib.request import Request, urlopen

SITE_URL = os.environ.get("FYZSXNB_SITE_URL", "https://fyzsxnb.com").rstrip("/")
API_BASE = f"{SITE_URL}/wp-json/wp/v2"
UA = "fyz-041-truth-fetcher/1.0"
OUTPUT_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "LOCALE-PRODUCTION-META-SNAPSHOT-041.json")


def auth_headers() -> dict[str, str]:
    u = os.environ.get("WP_USER") or os.environ.get("FYZSXNB_WP_USER")
    p = os.environ.get("WP_APP_PASSWORD") or os.environ.get("FYZSXNB_WP_APP_PASSWORD")
    if not u or not p:
        raise RuntimeError("WP_USER / WP_APP_PASSWORD not set. Run via run_wp_publisher_secure.ps1.")
    token = base64.b64encode(f"{u}:{p}".encode()).decode()
    return {"Authorization": f"Basic {token}"}


def request_json(method: str, path: str) -> tuple[int, dict | list, str]:
    headers = {"User-Agent": UA, "Accept": "application/json"}
    headers.update(auth_headers())
    req = Request(f"{API_BASE}{path}", headers=headers, method=method)
    try:
        with urlopen(req, timeout=45) as r:
            txt = r.read().decode("utf-8", "replace")
            pages = r.headers.get("X-WP-TotalPages", "1")
            return r.status, (json.loads(txt) if txt else {}), pages
    except HTTPError as e:
        body = e.read().decode("utf-8", "replace")
        try:
            return e.code, json.loads(body), "1"
        except Exception:
            return e.code, {"error": body}, "1"
    except URLError as e:
        return 0, {"error": str(e.reason)}, "1"


def fetch_all_published_posts():
    print(f"Fetching published posts with context=edit from {API_BASE}...")
    posts = []
    page = 1
    while True:
        params = {
            "per_page": 100,
            "page": page,
            "status": "publish",
            "context": "edit",
            "_fields": "id,slug,status,categories,meta",
        }
        status_code, rows, total_pages = request_json("GET", f"/posts?{urlencode(params)}")
        if status_code != 200 or not isinstance(rows, list):
            print(f"ERROR: Fetch failed on page {page} with status {status_code}: {rows}")
            sys.exit(1)

        posts.extend(rows)
        if page >= int(total_pages):
            break
        page += 1

    return posts


def main():
    posts = fetch_all_published_posts()
    print(f"Total published posts retrieved: {len(posts)}")

    # Verify that meta is observable
    meta_observable_count = 0
    snapshot_items = []

    for p in posts:
        pid = p.get("id")
        slug = p.get("slug")
        status = p.get("status")
        cats = p.get("categories", [])
        meta = p.get("meta", {})

        if not isinstance(meta, dict):
            print(f"ERROR: Post {pid} meta is not a dictionary.")
            sys.exit(1)

        # Check if _fyz_content_language is exposed in meta
        lang = meta.get("_fyz_content_language")
        kind = meta.get("_fyz_content_kind")

        if lang is not None or kind is not None or "_fyz_content_language" in meta:
            meta_observable_count += 1

        snapshot_items.append({
            "post_id": pid,
            "slug": slug,
            "status": status,
            "categories": cats,
            "content_language": lang if lang is not None else "",
            "content_kind": kind if kind is not None else "",
        })

    print(f"Posts with observable meta object: {len(snapshot_items)} / {len(posts)}")

    if meta_observable_count == 0:
        print("STOP: REAL_META_NOT_OBSERVABLE (No _fyz_content_language fields found in REST response).")
        sys.exit(1)

    # Sort by post_id descending
    snapshot_items.sort(key=lambda x: x["post_id"], reverse=True)

    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(snapshot_items, f, indent=2, ensure_ascii=False)

    print(f"Successfully written production snapshot to {OUTPUT_FILE}")


if __name__ == "__main__":
    main()
