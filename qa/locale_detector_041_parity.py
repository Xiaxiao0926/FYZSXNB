#!/usr/bin/env python3
"""locale_detector_041_parity.py — Generates full 96-post offline parity matrix for 0.4.1."""
from __future__ import annotations
import json
import os
import sys

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
INVENTORY_FILE = os.path.join(BASE_DIR, "feed_036_inventory_report.json")
OUTPUT_FILE = os.path.join(BASE_DIR, "LOCALE-DETECTOR-PARITY-041.json")

LEGACY_RU_IDS = {400, 448, 445, 442, 441, 434, 433, 432, 426, 420, 415, 405, 390, 372, 350}


def resolve_legacy_detector(post_id: int, cats: list[int]) -> bool:
    if post_id in LEGACY_RU_IDS:
        return True
    if 54 in cats:
        return True
    return False


def resolve_new_content_locale(post_id: int, meta_locale: str | None, cats: list[int]) -> dict:
    has_cat54 = 54 in cats
    norm_meta = (meta_locale or "").strip().lower()
    if norm_meta in ("ru", "ru-ru"):
        norm_meta = "ru"
    elif norm_meta in ("en", "en-us", "en-gb"):
        norm_meta = "en"
    else:
        norm_meta = ""

    # Step 1: Explicit metadata check
    if norm_meta == "ru":
        if has_cat54:
            return {"locale": "ru", "source": "meta", "valid": True, "conflict": False}
        else:
            # Conflict: meta=ru without Cat54
            legacy = resolve_legacy_detector(post_id, cats)
            return {"locale": "ru" if legacy else "en", "source": "legacy" if legacy else "default", "valid": False, "conflict": True, "conflict_reason": "ru_missing_cat54"}
    elif norm_meta == "en":
        if not has_cat54:
            return {"locale": "en", "source": "meta", "valid": True, "conflict": False}
        else:
            # Conflict: meta=en with Cat54
            legacy = resolve_legacy_detector(post_id, cats)
            return {"locale": "ru" if legacy else "en", "source": "legacy" if legacy else "default", "valid": False, "conflict": True, "conflict_reason": "en_has_cat54"}

    # Step 2: Metadata missing / unknown -> legacy fallback
    legacy = resolve_legacy_detector(post_id, cats)
    if legacy:
        return {"locale": "ru", "source": "legacy", "valid": True, "conflict": False}
    else:
        return {"locale": "en", "source": "default", "valid": True, "conflict": False}


def main():
    if not os.path.exists(INVENTORY_FILE):
        print(f"ERROR: Inventory file not found at {INVENTORY_FILE}")
        sys.exit(1)

    with open(INVENTORY_FILE, "r", encoding="utf-8") as f:
        data = json.load(f)

    posts = data.get("migration", [])

    items = []
    meta_en_count = 0
    meta_ru_count = 0
    meta_unk_count = 0

    legacy_ru_post_count = 0
    meta_ru_not_legacy = []
    legacy_ru_not_meta_ru = []
    structural_conflicts = []
    parity_mismatches = []

    for p in posts:
        pid = p["id"]
        cats = list(p.get("categories", []))
        if pid == 350 and 54 not in cats:
            cats.append(54)  # 0.3.6.1 baseline correction

        raw_loc = p.get("legacy_locale")
        if raw_loc == "ru-RU":
            meta_loc = "ru"
            meta_ru_count += 1
        elif raw_loc == "en-US":
            meta_loc = "en"
            meta_en_count += 1
        else:
            meta_loc = None
            meta_unk_count += 1

        is_legacy_id = pid in LEGACY_RU_IDS
        if is_legacy_id:
            legacy_ru_post_count += 1

        legacy_behavior = resolve_legacy_detector(pid, cats)
        new_res = resolve_new_content_locale(pid, meta_loc, cats)

        # Compare parity: legacy behavior vs new resolved locale
        legacy_locale_str = "ru" if legacy_behavior else "en"
        parity_ok = (new_res["locale"] == legacy_locale_str)

        if not parity_ok:
            parity_mismatches.append(pid)

        if new_res["conflict"]:
            structural_conflicts.append(pid)

        if meta_loc == "ru" and not is_legacy_id:
            meta_ru_not_legacy.append(pid)

        if is_legacy_id and meta_loc != "ru":
            legacy_ru_not_meta_ru.append(pid)

        items.append({
            "post_id": pid,
            "slug": p.get("slug"),
            "meta_locale": meta_loc,
            "cat54": 54 in cats,
            "is_legacy_id": is_legacy_id,
            "legacy_detector_ru": legacy_behavior,
            "new_resolved_locale": new_res["locale"],
            "resolver_source": new_res["source"],
            "valid": new_res["valid"],
            "conflict": new_res["conflict"],
            "parity": parity_ok,
        })

    summary = {
        "task_id": "FYZ-20260820-LOCALE-DETECTOR-041",
        "total_published": len(posts),
        "meta_en": meta_en_count,
        "meta_ru": meta_ru_count,
        "meta_unknown": meta_unk_count,
        "legacy_ru_posts": legacy_ru_post_count,
        "meta_ru_not_legacy_count": len(meta_ru_not_legacy),
        "meta_ru_not_legacy_ids": sorted(meta_ru_not_legacy),
        "legacy_ru_not_meta_ru_count": len(legacy_ru_not_meta_ru),
        "legacy_ru_not_meta_ru_ids": legacy_ru_not_meta_ru,
        "structural_conflict_count": len(structural_conflicts),
        "structural_conflict_ids": structural_conflicts,
        "parity_mismatch_count": len(parity_mismatches),
        "parity_mismatch_ids": parity_mismatches,
    }

    report = {
        "summary": summary,
        "posts": items,
    }

    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(report, f, indent=2, ensure_ascii=False)

    print("=== 0.4.1 Parity Report Generated ===")
    print(json.dumps(summary, indent=2))


if __name__ == "__main__":
    main()
