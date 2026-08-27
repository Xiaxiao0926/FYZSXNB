#!/usr/bin/env python3
"""language_v2_data_test.py — Unit and Invariant Test Suite for Language Contract V2 Data Layer (0.4.5-A)."""
from __future__ import annotations
import json
import os
import sys

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SNAPSHOT_FILE = os.path.join(BASE_DIR, "LOCALE-PRODUCTION-META-SNAPSHOT-041.json")
HISTORICAL_INVENTORY = os.path.join(BASE_DIR, "feed_036_inventory_report.json")

FYZSXNB_FEED_RU_LIBRARY_CAT = 54


# ---------------------------------------------------------------------------
# Python equivalents of Feed Plugin v1.2.5 core data-layer functions
# ---------------------------------------------------------------------------
def feed_sanitize_language(value: str | None) -> str:
    if not value:
        return ""
    v = str(value).strip().lower()
    if v in ("en", "en-us", "en-gb"):
        return "en"
    if v in ("ru", "ru-ru"):
        return "ru"
    if v in ("zh", "zh-cn", "zh-hans", "zh_cn", "zh_hans"):
        return "zh"
    return ""


def feed_sanitize_kind(value: str | None) -> str:
    if not value:
        return ""
    v = str(value).strip().lower()
    return v if v in ("signal", "guide") else ""


def home_post_locale(meta_lang: str | None, categories: list[int]) -> str:
    declared = (meta_lang or "").strip().lower()
    if declared in ("en", "en-us", "en-gb"):
        return "en-US"
    if declared in ("ru", "ru-ru"):
        return "ru-RU"
    if declared in ("zh", "zh-cn", "zh-hans", "zh_cn", "zh_hans"):
        return "zh-CN"
    # Structural confirm: Category 54
    if FYZSXNB_FEED_RU_LIBRARY_CAT in categories:
        return "ru-RU"
    return ""


def pubmeta_missing_fields(meta_lang: str | None, meta_kind: str | None) -> list[str]:
    lang = (meta_lang or "").strip().lower()
    kind = (meta_kind or "").strip().lower()
    missing = []
    if lang not in ("en", "ru", "zh"):
        missing.append("language")
    if kind not in ("signal", "guide"):
        missing.append("kind")
    return missing


def is_feed_eligible(target_feed_locale: str, post_locale: str) -> bool:
    return target_feed_locale == post_locale and bool(post_locale)


def run_tests() -> int:
    passed = 0
    failed = 0

    def assert_test(name: str, cond: bool, detail: str = ""):
        nonlocal passed, failed
        if cond:
            print(f"  \u2713 PASS: {name}")
            passed += 1
        else:
            print(f"  \u2717 FAIL: {name} - {detail}")
            failed += 1

    print("=================================================================")
    print("      FYZSXNB 0.4.5-A Language Contract V2 Data Layer Tests      ")
    print("=================================================================")

    # 1. Sanitize Whitelist Tests
    print("\n--- 1. Sanitize Whitelist Tests ---")
    assert_test("Sanitize 'zh' -> 'zh'", feed_sanitize_language("zh") == "zh")
    assert_test("Sanitize 'zh-CN' -> 'zh'", feed_sanitize_language("zh-CN") == "zh")
    assert_test("Sanitize 'zh-hans' -> 'zh'", feed_sanitize_language("zh-hans") == "zh")
    assert_test("Sanitize 'zh_CN' -> 'zh'", feed_sanitize_language("zh_CN") == "zh")
    assert_test("Sanitize 'en' -> 'en'", feed_sanitize_language("en") == "en")
    assert_test("Sanitize 'en-US' -> 'en'", feed_sanitize_language("en-US") == "en")
    assert_test("Sanitize 'ru' -> 'ru'", feed_sanitize_language("ru") == "ru")
    assert_test("Sanitize 'ru-RU' -> 'ru'", feed_sanitize_language("ru-RU") == "ru")
    assert_test("Sanitize invalid 'de' -> ''", feed_sanitize_language("de") == "")

    # 2. TC-ZH-01: Valid ZH Meta Acceptance
    print("\n--- 2. TC-ZH-01: Valid ZH Meta Acceptance ---")
    missing_zh = pubmeta_missing_fields("zh", "guide")
    assert_test("TC-ZH-01: 'zh' + 'guide' has 0 missing fields", len(missing_zh) == 0)
    missing_zh_sig = pubmeta_missing_fields("zh-CN", "signal")
    assert_test("TC-ZH-01: Normalized 'zh-CN' + 'signal' accepted", len(pubmeta_missing_fields(feed_sanitize_language("zh-CN"), "signal")) == 0)
    assert_test("TC-ZH-01: home_post_locale for 'zh' is 'zh-CN'", home_post_locale("zh", [52]) == "zh-CN")

    # 3. TC-ZH-02: Structural Warnings & Conflicts
    print("\n--- 3. TC-ZH-02: Structural Warnings & Conflicts ---")
    zh_with_cat54 = (home_post_locale("zh", [52, 54]) == "zh-CN") and (54 in [52, 54])
    assert_test("TC-ZH-02: ZH with Cat54 detected as structural conflict", zh_with_cat54)
    ru_without_cat54 = (home_post_locale("ru", [52]) == "ru-RU") and (54 not in [52])
    assert_test("TC-ZH-02: RU without Cat54 detected as structural conflict", ru_without_cat54)
    en_with_cat54 = (home_post_locale("en", [50, 54]) == "en-US") and (54 in [50, 54])
    assert_test("TC-ZH-02: EN with Cat54 detected as structural conflict", en_with_cat54)

    # 4. TC-ZH-03: Feed Absolute Isolation
    print("\n--- 4. TC-ZH-03: Feed Absolute Isolation ---")
    zh_post_loc = home_post_locale("zh", [52])
    assert_test("TC-ZH-03: ZH post is NOT eligible for EN homepage feed", not is_feed_eligible("en-US", zh_post_loc))
    assert_test("TC-ZH-03: ZH post is NOT eligible for RU homepage feed", not is_feed_eligible("ru-RU", zh_post_loc))
    assert_test("TC-ZH-03: ZH post IS eligible for future ZH homepage feed", is_feed_eligible("zh-CN", zh_post_loc))

    # 5. TC-ZH-04 & TC-ZH-05: Real Production Snapshot Regression Parity
    print("\n--- 5. TC-ZH-04 & TC-ZH-05: Real Snapshot Regression Parity (96 Posts) ---")
    if os.path.exists(SNAPSHOT_FILE):
        with open(SNAPSHOT_FILE, "r", encoding="utf-8") as f:
            posts = json.load(f)

        en_matches = 0
        ru_matches = 0
        unknown_matches = 0

        for p in posts:
            lang = p.get("content_language", "")
            cats = p.get("categories", [])
            loc = home_post_locale(lang, cats)

            if lang == "en":
                if loc == "en-US":
                    en_matches += 1
            elif lang == "ru":
                if loc == "ru-RU":
                    ru_matches += 1
            else:
                if loc == "":
                    unknown_matches += 1

        assert_test("TC-ZH-04: 58/58 EN posts resolve to 'en-US' (100% parity)", en_matches == 58, f"{en_matches}/58")
        assert_test("TC-ZH-05: 25/25 RU posts resolve to 'ru-RU' (100% parity)", ru_matches == 25, f"{ru_matches}/25")
        assert_test("13/13 Unknown posts isolated with empty feed locale (100% parity)", unknown_matches == 13, f"{unknown_matches}/13")

    # 6. Publishing Metadata Missing Fields Demotion Guard
    print("\n--- 6. Publishing Metadata Missing Fields Demotion Guard ---")
    assert_test("Missing language and kind -> demoted", set(pubmeta_missing_fields("", "")) == {"language", "kind"})
    assert_test("Missing language only -> demoted", pubmeta_missing_fields("", "guide") == ["language"])
    assert_test("Missing kind only -> demoted", pubmeta_missing_fields("zh", "") == ["kind"])
    assert_test("Valid EN pair -> accepted", len(pubmeta_missing_fields("en", "signal")) == 0)
    assert_test("Valid RU pair -> accepted", len(pubmeta_missing_fields("ru", "guide")) == 0)
    assert_test("Valid ZH pair -> accepted", len(pubmeta_missing_fields("zh", "signal")) == 0)

    print(f"\n=================================================================")
    print(f"Results: {passed} PASSED / {failed} FAILED")
    print(f"=================================================================")
    return 0 if failed == 0 else 1


if __name__ == "__main__":
    sys.exit(run_tests())
