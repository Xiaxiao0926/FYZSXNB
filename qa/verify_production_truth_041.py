#!/usr/bin/env python3
"""verify_production_truth_041.py — Comprehensive analysis of live production metadata truth for 0.4.1."""
from __future__ import annotations
import json
import os
import sys

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SNAPSHOT_FILE = os.path.join(BASE_DIR, "LOCALE-PRODUCTION-META-SNAPSHOT-041.json")
HISTORICAL_FILE = os.path.join(BASE_DIR, "feed_036_inventory_report.json")

LEGACY_RU_IDS = {400, 448, 445, 442, 441, 434, 433, 432, 426, 420, 415, 405, 390, 372, 350}
EXPECTED_11_NEW_RU = [484, 485, 489, 491, 492, 500, 503, 504, 510, 512, 514]
EXPECTED_14_LEGACY_RU = [448, 445, 442, 441, 434, 433, 432, 426, 420, 415, 405, 390, 372, 350]


def main():
    if not os.path.exists(SNAPSHOT_FILE):
        print(f"ERROR: Snapshot file {SNAPSHOT_FILE} does not exist.")
        sys.exit(1)

    with open(SNAPSHOT_FILE, "r", encoding="utf-8") as f:
        posts = json.load(f)

    # Historical cross check
    hist_map = {}
    if os.path.exists(HISTORICAL_FILE):
        with open(HISTORICAL_FILE, "r", encoding="utf-8") as f:
            hdata = json.load(f)
            for hp in hdata.get("migration", []):
                hist_map[hp["id"]] = hp.get("legacy_locale")

    total_published = len(posts)
    actual_meta_en = 0
    actual_meta_ru = 0
    actual_meta_unknown = 0

    actual_ru_with_cat54 = []
    actual_ru_without_cat54 = []
    actual_en_without_cat54 = []
    actual_en_with_cat54 = []

    legacy_id_ru_and_meta_ru = []
    legacy_id_ru_but_meta_en = []
    legacy_id_ru_but_meta_unknown = []

    meta_ru_not_legacy_id = []

    structural_conflicts = []
    parity_mismatches = []

    hist_matches = 0
    hist_mismatches = []

    for p in posts:
        pid = p["post_id"]
        lang = p.get("content_language", "").strip().lower()
        cats = p.get("categories", [])
        has_cat54 = 54 in cats
        is_legacy_id = pid in LEGACY_RU_IDS

        # Classification
        if lang in ("ru", "ru-ru"):
            norm_lang = "ru"
            actual_meta_ru += 1
            if has_cat54:
                actual_ru_with_cat54.append(pid)
            else:
                actual_ru_without_cat54.append(pid)
                structural_conflicts.append((pid, "RU meta without Cat54"))

            if is_legacy_id:
                legacy_id_ru_and_meta_ru.append(pid)
            else:
                meta_ru_not_legacy_id.append(pid)

        elif lang in ("en", "en-us", "en-gb"):
            norm_lang = "en"
            actual_meta_en += 1
            if not has_cat54:
                actual_en_without_cat54.append(pid)
            else:
                actual_en_with_cat54.append(pid)
                structural_conflicts.append((pid, "EN meta with Cat54"))

            if is_legacy_id:
                legacy_id_ru_but_meta_en.append(pid)
                structural_conflicts.append((pid, "Legacy RU ID has EN meta"))
        else:
            norm_lang = ""
            actual_meta_unknown += 1
            if is_legacy_id:
                legacy_id_ru_but_meta_unknown.append(pid)

        # Resolver simulation on live data
        # Legacy behavior: is_legacy_id or has_cat54 -> ru, else en
        legacy_is_ru = is_legacy_id or has_cat54

        # New resolver behavior:
        # If norm_lang == 'ru' and has_cat54 -> ru (source: meta)
        # If norm_lang == 'en' and not has_cat54 -> en (source: meta)
        # If conflict -> legacy fallback
        # If unk -> legacy fallback
        if norm_lang == "ru" and has_cat54:
            new_resolved_ru = True
        elif norm_lang == "en" and not has_cat54:
            new_resolved_ru = False
        else:
            new_resolved_ru = legacy_is_ru

        if new_resolved_ru != legacy_is_ru:
            parity_mismatches.append((pid, f"New resolved RU={new_resolved_ru}, Legacy RU={legacy_is_ru}"))

        # Historical comparison
        hist_val = hist_map.get(pid)
        # normalize hist_val: "ru-RU" -> "ru", "en-US" -> "en", None -> ""
        hist_norm = "ru" if hist_val == "ru-RU" else ("en" if hist_val == "en-US" else "")
        if hist_norm == norm_lang:
            hist_matches += 1
        else:
            hist_mismatches.append((pid, hist_val, lang))

    print("=================================================================")
    print("      FYZSXNB 0.4.1 PRODUCTION METADATA EVIDENCE VERIFICATION     ")
    print("=================================================================")
    print(f"TOTAL_PUBLISHED:                 {total_published}")
    print(f"ACTUAL_META_EN:                  {actual_meta_en}")
    print(f"ACTUAL_META_RU:                  {actual_meta_ru}")
    print(f"ACTUAL_META_UNKNOWN:             {actual_meta_unknown}")
    print(f"-----------------------------------------------------------------")
    print(f"ACTUAL_RU_WITH_CAT54:            {len(actual_ru_with_cat54)}")
    print(f"ACTUAL_RU_WITHOUT_CAT54:         {len(actual_ru_without_cat54)} -> {actual_ru_without_cat54}")
    print(f"ACTUAL_EN_WITHOUT_CAT54:         {len(actual_en_without_cat54)}")
    print(f"ACTUAL_EN_WITH_CAT54:            {len(actual_en_with_cat54)} -> {actual_en_with_cat54}")
    print(f"-----------------------------------------------------------------")
    print(f"LEGACY_ID_RU_AND_META_RU:        {len(legacy_id_ru_and_meta_ru)} (out of 14 published legacy posts)")
    print(f"LEGACY_ID_RU_BUT_META_EN:        {len(legacy_id_ru_but_meta_en)} -> {legacy_id_ru_but_meta_en}")
    print(f"LEGACY_ID_RU_BUT_META_UNKNOWN:   {len(legacy_id_ru_but_meta_unknown)} -> {legacy_id_ru_but_meta_unknown}")
    print(f"-----------------------------------------------------------------")
    print(f"META_RU_NOT_LEGACY_ID:           {len(meta_ru_not_legacy_id)} -> {sorted(meta_ru_not_legacy_id)}")
    print(f"STRUCTURAL_CONFLICT:             {len(structural_conflicts)} -> {structural_conflicts}")
    print(f"RESOLVER_PARITY_MISMATCH:        {len(parity_mismatches)} -> {parity_mismatches}")
    print(f"-----------------------------------------------------------------")
    print(f"HISTORICAL_INVENTORY_MATCH:      {hist_matches}")
    print(f"HISTORICAL_INVENTORY_MISMATCH:   {len(hist_mismatches)} -> {hist_mismatches}")
    print(f"=================================================================")

    # 5. Verification of 11 new RU posts
    print("\n--- 5. Verifying 11 New RU Posts in Live Production Snapshot ---")
    new_ru_verified = True
    for pid in sorted(EXPECTED_11_NEW_RU):
        match = next((p for p in posts if p["post_id"] == pid), None)
        if not match:
            print(f"  [!] Post {pid} NOT found in production snapshot!")
            new_ru_verified = False
            continue
        lang = match.get("content_language")
        has_54 = 54 in match.get("categories", [])
        in_legacy = pid in LEGACY_RU_IDS
        ok = (lang == "ru" and has_54 and not in_legacy)
        print(f"  Post {pid}: lang='{lang}', cat54={has_54}, in_legacy_list={in_legacy} -> {'PASS' if ok else 'FAIL'}")
        if not ok:
            new_ru_verified = False

    # 6. Verification of 14 legacy RU posts
    print("\n--- 6. Verifying 14 Legacy RU Posts in Live Production Snapshot ---")
    legacy_ru_verified = True
    for pid in sorted(EXPECTED_14_LEGACY_RU):
        match = next((p for p in posts if p["post_id"] == pid), None)
        if not match:
            print(f"  [!] Post {pid} NOT found in production snapshot!")
            legacy_ru_verified = False
            continue
        lang = match.get("content_language")
        has_54 = 54 in match.get("categories", [])
        ok = (lang == "ru" and has_54)
        print(f"  Post {pid}: lang='{lang}', cat54={has_54} -> {'PASS' if ok else 'FAIL'}")
        if not ok:
            legacy_ru_verified = False

    print(f"\n11 New RU Posts Live Verification:     {'ALL PASS' if new_ru_verified else 'FAILED'}")
    print(f"14 Legacy RU Posts Live Verification:  {'ALL PASS' if legacy_ru_verified else 'FAILED'}")


if __name__ == "__main__":
    main()
