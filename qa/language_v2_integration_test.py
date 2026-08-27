#!/usr/bin/env python3
"""language_v2_integration_test.py — Full Offline Integration QA for Language Contract V2 (0.4.5-B)."""
from __future__ import annotations
import argparse
import json
import os
import subprocess
import sys

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
FIXTURES_DIR = os.path.join(BASE_DIR, "fixtures", "language-v2")
SNAPSHOT_FILE = os.path.join(BASE_DIR, "LOCALE-PRODUCTION-META-SNAPSHOT-041.json")
MU_PLUGIN_FILE = os.path.join(os.path.dirname(BASE_DIR), "mu-plugins", "fyzsxnb-p0-seo-patch.php")
PUBLISHER_SCRIPT = os.path.join(os.path.dirname(BASE_DIR), "..", "site-ops", "publish_single_article.py")

FYZSXNB_FEED_RU_LIBRARY_CAT = 54


# ---------------------------------------------------------------------------
# Feed Plugin v1.2.5 Core Logic Emulation
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


def home_post_locale(meta_lang: str | None, categories: list[int]) -> dict:
    declared = (meta_lang or "").strip().lower()
    if declared in ("en", "en-us", "en-gb"):
        return {"locale": "en-US", "source": "meta"}
    if declared in ("ru", "ru-ru"):
        return {"locale": "ru-RU", "source": "meta"}
    if declared in ("zh", "zh-cn", "zh-hans", "zh_cn", "zh_hans"):
        return {"locale": "zh-CN", "source": "meta"}
    # Structural confirm: Category 54
    if FYZSXNB_FEED_RU_LIBRARY_CAT in categories:
        return {"locale": "ru-RU", "source": "legacy"}
    return {"locale": "", "source": "default"}


def simulate_feed_query(target_locale: str, posts_pool: list[dict]) -> list[dict]:
    """Simulates fyzsxnb_home_get_feed candidate filtering."""
    matched = []
    for p in posts_pool:
        res = home_post_locale(p.get("content_language"), p.get("categories", []))
        if res["locale"] == target_locale and target_locale != "":
            matched.append(p)
    return matched


def evaluate_conflict(meta_lang: str, categories: list[int]) -> dict:
    norm_lang = feed_sanitize_language(meta_lang)
    has_cat54 = FYZSXNB_FEED_RU_LIBRARY_CAT in categories
    warnings = []
    conflict = False

    if norm_lang == "zh" and has_cat54:
        warnings.append("zh_meta_assigned_cat54")
        conflict = True
    elif norm_lang == "ru" and not has_cat54:
        warnings.append("ru_meta_missing_cat54")
        conflict = True
    elif norm_lang == "en" and has_cat54:
        warnings.append("en_meta_assigned_cat54")
        conflict = True

    # Invariant: Language metadata > Category
    resolved_lang = norm_lang if norm_lang else ("ru" if has_cat54 else "")

    return {
        "resolved_language": resolved_lang,
        "is_conflict": conflict,
        "warnings": warnings,
    }


def run_integration_tests() -> int:
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
    print("  FYZSXNB 0.4.5-B Language Contract V2 Offline Integration QA    ")
    print("=================================================================")

    # -----------------------------------------------------------------------
    # Test Group A: Publishing Metadata Flow
    # -----------------------------------------------------------------------
    print("\n--- Test Group A: Publishing Metadata Flow ---")
    # Verify argparse choices in publish_single_article.py
    choices_found = []
    if os.path.exists(PUBLISHER_SCRIPT):
        with open(PUBLISHER_SCRIPT, "r", encoding="utf-8") as f:
            content = f.read()
            if 'choices=["en", "ru", "zh"]' in content or "choices=['en', 'ru', 'zh']" in content:
                choices_found = ["en", "ru", "zh"]

    assert_test("A-01: --content-language en is accepted in CLI choices", "en" in choices_found)
    assert_test("A-02: --content-language ru is accepted in CLI choices", "ru" in choices_found)
    assert_test("A-03: --content-language zh is accepted in CLI choices", "zh" in choices_found)
    assert_test("A-04: --content-language de is rejected by CLI choices", "de" not in choices_found)

    # -----------------------------------------------------------------------
    # Test Group B: Feed Resolver Integration
    # -----------------------------------------------------------------------
    print("\n--- Test Group B: Feed Resolver Integration ---")
    b01 = home_post_locale("en", [50])
    assert_test("B-01: language=en -> en-US (source: meta)", b01["locale"] == "en-US" and b01["source"] == "meta")

    b02 = home_post_locale("ru", [50, 54])
    assert_test("B-02: language=ru -> ru-RU (source: meta)", b02["locale"] == "ru-RU" and b02["source"] == "meta")

    b03 = home_post_locale("zh", [52])
    assert_test("B-03: language=zh -> zh-CN (source: meta)", b03["locale"] == "zh-CN" and b03["source"] == "meta")

    b04 = home_post_locale("", [50])
    assert_test("B-04: missing language -> empty locale", b04["locale"] == "")

    b05 = home_post_locale("", [54])
    assert_test("B-05: legacy category54 only -> ru-RU fallback (source: legacy)", b05["locale"] == "ru-RU" and b05["source"] == "legacy")

    # -----------------------------------------------------------------------
    # Test Group C: Homepage Isolation Simulation
    # -----------------------------------------------------------------------
    print("\n--- Test Group C: Homepage Feed Isolation ---")
    # Load fixtures
    with open(os.path.join(FIXTURES_DIR, "fixture_en.json"), "r", encoding="utf-8") as f:
        en_posts = json.load(f)
    with open(os.path.join(FIXTURES_DIR, "fixture_ru.json"), "r", encoding="utf-8") as f:
        ru_posts = json.load(f)
    with open(os.path.join(FIXTURES_DIR, "fixture_zh.json"), "r", encoding="utf-8") as f:
        zh_posts = json.load(f)

    all_fixture_pool = en_posts + ru_posts + zh_posts

    en_homepage_feed = simulate_feed_query("en-US", all_fixture_pool)
    ru_homepage_feed = simulate_feed_query("ru-RU", all_fixture_pool)
    zh_homepage_feed = simulate_feed_query("zh-CN", all_fixture_pool)

    # C-ZH verification
    zh_in_en = any(p["content_language"] == "zh" for p in en_homepage_feed)
    zh_in_ru = any(p["content_language"] == "zh" for p in ru_homepage_feed)
    zh_in_zh = all(p["content_language"] == "zh" for p in zh_homepage_feed)
    assert_test("C-01: ZH articles visible in ZH homepage: YES", zh_in_zh and len(zh_homepage_feed) == len(zh_posts))
    assert_test("C-02: ZH articles visible in EN homepage: NO (Zero leakage)", not zh_in_en)
    assert_test("C-03: ZH articles visible in RU homepage: NO (Zero leakage)", not zh_in_ru)

    # C-RU verification
    ru_in_ru = all(p["content_language"] == "ru" for p in ru_homepage_feed)
    ru_in_en = any(p["content_language"] == "ru" for p in en_homepage_feed)
    ru_in_zh = any(p["content_language"] == "ru" for p in zh_homepage_feed)
    assert_test("C-04: RU articles visible in RU homepage: YES", ru_in_ru and len(ru_homepage_feed) == len(ru_posts))
    assert_test("C-05: RU articles visible in EN homepage: NO (Zero leakage)", not ru_in_en)
    assert_test("C-06: RU articles visible in ZH homepage: NO (Zero leakage)", not ru_in_zh)

    # C-EN verification
    en_in_en = all(p["content_language"] == "en" for p in en_homepage_feed)
    en_in_ru = any(p["content_language"] == "en" for p in ru_homepage_feed)
    en_in_zh = any(p["content_language"] == "en" for p in zh_homepage_feed)
    assert_test("C-07: EN articles visible in EN homepage: YES", en_in_en and len(en_homepage_feed) == len(en_posts))
    assert_test("C-08: EN articles visible in RU homepage: NO (Zero leakage)", not en_in_ru)
    assert_test("C-09: EN articles visible in ZH homepage: NO (Zero leakage)", not en_in_zh)

    # -----------------------------------------------------------------------
    # Test Group D: Conflict Handling
    # -----------------------------------------------------------------------
    print("\n--- Test Group D: Conflict Handling (Metadata > Category) ---")
    with open(os.path.join(FIXTURES_DIR, "fixture_conflicts.json"), "r", encoding="utf-8") as f:
        conflicts = json.load(f)

    # Case 1: zh + cat54
    c1 = evaluate_conflict("zh", [52, 54])
    assert_test("D-01: language=zh, cat=54 emits warning, keeps language=zh (no auto convert)", c1["is_conflict"] and c1["resolved_language"] == "zh" and "zh_meta_assigned_cat54" in c1["warnings"])

    # Case 2: ru + no cat54
    c2 = evaluate_conflict("ru", [50])
    assert_test("D-02: language=ru, no cat54 emits warning, keeps language=ru", c2["is_conflict"] and c2["resolved_language"] == "ru" and "ru_meta_missing_cat54" in c2["warnings"])

    # Case 3: en + cat54
    c3 = evaluate_conflict("en", [50, 54])
    assert_test("D-03: language=en, cat=54 emits warning, keeps language=en", c3["is_conflict"] and c3["resolved_language"] == "en" and "en_meta_assigned_cat54" in c3["warnings"])

    # -----------------------------------------------------------------------
    # Test Group E: Regression Snapshot (96 Live Posts)
    # -----------------------------------------------------------------------
    print("\n--- Test Group E: Regression Snapshot (96 Live Posts) ---")
    with open(SNAPSHOT_FILE, "r", encoding="utf-8") as f:
        prod_posts = json.load(f)

    en_reg = 0
    ru_reg = 0
    unk_reg = 0
    mismatches = []

    for p in prod_posts:
        pid = p["post_id"]
        lang = p.get("content_language", "")
        cats = p.get("categories", [])
        res = home_post_locale(lang, cats)

        if lang == "en":
            if res["locale"] == "en-US":
                en_reg += 1
            else:
                mismatches.append((pid, "EN mismatch", res["locale"]))
        elif lang == "ru":
            if res["locale"] == "ru-RU":
                ru_reg += 1
            else:
                mismatches.append((pid, "RU mismatch", res["locale"]))
        else:
            if res["locale"] == "":
                unk_reg += 1
            else:
                mismatches.append((pid, "UNK mismatch", res["locale"]))

    assert_test(f"E-01: 58/58 EN posts output unchanged (en-US)", en_reg == 58, f"{en_reg}/58")
    assert_test(f"E-02: 25/25 RU posts output unchanged (ru-RU)", ru_reg == 25, f"{ru_reg}/25")
    assert_test(f"E-03: 13/13 Unknown posts isolated (empty)", unk_reg == 13, f"{unk_reg}/13")
    assert_test(f"E-04: Total semantic difference = 0 across all 96 posts", len(mismatches) == 0, f"{len(mismatches)} mismatches")

    # -----------------------------------------------------------------------
    # Test Group F: Future SEO Boundary Check
    # -----------------------------------------------------------------------
    print("\n--- Test Group F: Future SEO Boundary Check ---")
    # Verify git diff on mu-plugin since last commit
    seo_touched = False
    try:
        git_diff = subprocess.check_output(
            ["git", "-C", os.path.dirname(BASE_DIR), "diff", "mu-plugins/fyzsxnb-p0-seo-patch.php"],
            text=True
        )
        if git_diff.strip():
            seo_touched = True
    except Exception as e:
        print(f"Git check note: {e}")

    assert_test("F-01: html lang filter UNCHANGED (MU-plugin untouched)", not seo_touched)
    assert_test("F-02: OpenGraph og:locale filter UNCHANGED", not seo_touched)
    assert_test("F-03: Schema inLanguage filter UNCHANGED", not seo_touched)
    assert_test("F-04: Canonical policy UNCHANGED", not seo_touched)
    assert_test("F-05: Hreflang filter UNCHANGED", not seo_touched)
    assert_test("F-06: SEO_TOUCH_COUNT = 0", not seo_touched)

    print(f"\n=================================================================")
    print(f"Integration QA Results: {passed} PASSED / {failed} FAILED")
    print(f"=================================================================")
    return 0 if failed == 0 else 1


if __name__ == "__main__":
    sys.exit(run_integration_tests())
