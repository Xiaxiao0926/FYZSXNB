#!/usr/bin/env python3
"""locale_detector_041_test.py — Unit and Invariant Test Suite for 0.4.1 Central Locale Resolver."""
from __future__ import annotations
import sys

LEGACY_RU_IDS = {400, 448, 445, 442, 441, 434, 433, 432, 426, 420, 415, 405, 390, 372, 350}


class MockPost:
    def __init__(self, post_id: int, meta_lang: str | None, cats: list[int], post_type: str = "post"):
        self.id = post_id
        self.meta_lang = meta_lang
        self.cats = cats
        self.post_type = post_type


def resolve_content_locale(post: MockPost) -> dict:
    pid = post.id
    if pid <= 0:
        return {"locale": "en", "source": "default", "valid": True, "conflict": False}

    meta_lang = (post.meta_lang or "").strip().lower()
    has_cat54 = 54 in post.cats

    norm_meta = ""
    if meta_lang in ("ru", "ru-ru"):
        norm_meta = "ru"
    elif meta_lang in ("en", "en-us", "en-gb"):
        norm_meta = "en"

    # 1. Explicit metadata check
    if norm_meta == "ru":
        if has_cat54:
            return {"locale": "ru", "source": "meta", "valid": True, "conflict": False}
        else:
            legacy_is_ru = (pid in LEGACY_RU_IDS) or has_cat54
            return {
                "locale": "ru" if legacy_is_ru else "en",
                "source": "legacy" if legacy_is_ru else "default",
                "valid": False,
                "conflict": True,
                "reason": "ru_meta_missing_cat54",
            }
    elif norm_meta == "en":
        if not has_cat54:
            return {"locale": "en", "source": "meta", "valid": True, "conflict": False}
        else:
            legacy_is_ru = (pid in LEGACY_RU_IDS) or has_cat54
            return {
                "locale": "ru" if legacy_is_ru else "en",
                "source": "legacy" if legacy_is_ru else "default",
                "valid": False,
                "conflict": True,
                "reason": "en_meta_has_cat54",
            }

    # 2. Metadata missing / unknown -> legacy fallback
    if (pid in LEGACY_RU_IDS) or has_cat54:
        return {"locale": "ru", "source": "legacy", "valid": True, "conflict": False}

    return {"locale": "en", "source": "default", "valid": True, "conflict": False}


def is_russian_target(post: MockPost) -> bool:
    if post.post_type == "post":
        res = resolve_content_locale(post)
        return res["locale"] == "ru"

    # Non-post / page
    if post.id in LEGACY_RU_IDS or 54 in post.cats:
        return True
    return False


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

    print("===== FYZSXNB 0.4.1 Central Locale Resolver Synthetic Tests =====")

    # Case A: New RU post without hardcoded ID
    print("\n--- Case A: New RU post without hardcoded ID ---")
    p_a = MockPost(514, "ru", [50, 54])
    res_a = resolve_content_locale(p_a)
    assert_test("Case A: Resolved locale is 'ru'", res_a["locale"] == "ru")
    assert_test("Case A: Source is 'meta'", res_a["source"] == "meta")
    assert_test("Case A: Contract is valid and non-conflicting", res_a["valid"] and not res_a["conflict"])
    assert_test("Case A: is_russian_target returns True", is_russian_target(p_a))

    # Case B: New EN post without hardcoded ID
    print("\n--- Case B: New EN post without hardcoded ID ---")
    p_b = MockPost(513, "en", [50])
    res_b = resolve_content_locale(p_b)
    assert_test("Case B: Resolved locale is 'en'", res_b["locale"] == "en")
    assert_test("Case B: Source is 'meta'", res_b["source"] == "meta")
    assert_test("Case B: Contract is valid and non-conflicting", res_b["valid"] and not res_b["conflict"])
    assert_test("Case B: is_russian_target returns False", not is_russian_target(p_b))

    # Case C: Metadata missing, but post is in legacy RU ID list
    print("\n--- Case C: Metadata missing, in legacy RU ID list ---")
    p_c = MockPost(448, None, [50, 54])
    res_c = resolve_content_locale(p_c)
    assert_test("Case C: Resolved locale is 'ru'", res_c["locale"] == "ru")
    assert_test("Case C: Source is 'legacy'", res_c["source"] == "legacy")
    assert_test("Case C: is_russian_target returns True", is_russian_target(p_c))

    # Case D: Metadata missing, not in legacy list
    print("\n--- Case D: Metadata missing, not in legacy list ---")
    p_d = MockPost(999, None, [50])
    res_d = resolve_content_locale(p_d)
    assert_test("Case D: Resolved locale is 'en'", res_d["locale"] == "en")
    assert_test("Case D: Source is 'default'", res_d["source"] == "default")
    assert_test("Case D: is_russian_target returns False", not is_russian_target(p_d))

    # Case E: Structural conflict (RU meta without Cat 54)
    print("\n--- Case E: Structural conflict (RU meta without Cat 54) ---")
    p_e = MockPost(448, "ru", [50])  # in legacy list, but lacks 54
    res_e = resolve_content_locale(p_e)
    assert_test("Case E: Flagged as invalid conflict", not res_e["valid"] and res_e["conflict"])
    assert_test("Case E: Safe legacy fallback maintains 'ru'", res_e["locale"] == "ru" and res_e["source"] == "legacy")
    assert_test("Case E: Conflict reason recorded", res_e["reason"] == "ru_meta_missing_cat54")

    # Case F: Structural conflict (EN meta with Cat 54)
    print("\n--- Case F: Structural conflict (EN meta with Cat 54) ---")
    p_f = MockPost(888, "en", [50, 54])
    res_f = resolve_content_locale(p_f)
    assert_test("Case F: Flagged as invalid conflict", not res_f["valid"] and res_f["conflict"])
    assert_test("Case F: Conflict reason recorded", res_f["reason"] == "en_meta_has_cat54")

    # Non-post context: Page 400
    print("\n--- Page 400 Special Case ---")
    page_400 = MockPost(400, None, [], post_type="page")
    assert_test("Page 400 recognized as Russian target", is_russian_target(page_400))

    print(f"\n=======================================================")
    print(f"Results: {passed} PASSED / {failed} FAILED")
    return 0 if failed == 0 else 1


if __name__ == "__main__":
    sys.exit(run_tests())
