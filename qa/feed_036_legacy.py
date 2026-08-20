# -*- coding: utf-8 -*-
"""feed_036_legacy.py — byte-faithful reimplementation of the plugin's LEGACY
decision functions (as deployed on production) for the 0.3.6 migration audit.

IMPORTANT PHP quirks mirrored exactly:
  * PHP strtolower() only lowercases ASCII; Cyrillic case is left as-is.
  * RU guide regex must therefore match the ORIGINAL Cyrillic casing.
"""
import re

RU_CAT = 54
CJK_RE = re.compile(r"[\u3400-\u9fff]")
CYR_RE = re.compile(r"[\u0400-\u04ff]")
LAT_RE = re.compile(r"[A-Za-z]", re.UNICODE)
EN_GUIDE_RE = re.compile(r"(guide|checklist|readiness|procurement|verification|decision-map|how-to|compared)")
RU_GUIDE_RE = re.compile(r"(guide|check|repair|verification|гайд|руковод|провер|ремонт|совместим|выбор|ввоз|утильсбор)")


def ascii_lower(s: str) -> str:
    """Mirror PHP strtolower(): lowercase ASCII only, leave non-ASCII as-is."""
    return re.sub(r"[A-Z]", lambda m: m.group(0).lower(), s)


def legacy_locale(post: dict) -> str:
    """'ru-RU' | 'en-US' | '' exactly as fyzsxnb_home_post_locale (meta absent)."""
    title = (post.get("title") or {}).get("rendered") or ""
    if RU_CAT in (post.get("categories") or []):
        return "ru-RU"
    if CYR_RE.search(title):
        return "ru-RU"
    if CJK_RE.search(title):
        return ""
    return "en-US" if LAT_RE.search(title) else ""


def legacy_guide(post: dict, locale: str) -> bool:
    if not locale:
        return False
    slug = post.get("slug") or ""
    title = (post.get("title") or {}).get("rendered") or ""
    hay = ascii_lower(f"{slug} {title}")
    rx = RU_GUIDE_RE if locale == "ru-RU" else EN_GUIDE_RE
    return bool(rx.search(hay))
