# -*- coding: utf-8 -*-
"""build_036_interim.py — derives the interim plugin (v1.1.0) from the canonical
final plugin (v1.2.0) by inserting the LEGACY fallback decision path, marked
clearly, so the interim deploy is byte-behaviour-identical to v1.0.0 until the
explicit meta backfill completes."""
import os

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "..", "plugin", "fyzsxnb-home-dynamic-feeds", "fyzsxnb-home-dynamic-feeds.php")
DST_DIR = os.path.join(HERE, "..", "..", "deployments", "fyzsxnb-ui2-036", "interim")
DST = os.path.join(DST_DIR, "fyzsxnb-home-dynamic-feeds.php")

src = open(SRC, encoding="utf-8").read()

# 1) version header
src = src.replace("Version: 1.2.0", "Version: 1.1.0")
src = src.replace(
    "UI V2 0.3.6 Feed Hardening — v1.2.0 (explicit-only decision path).",
    "UI V2 0.3.6 Feed Hardening — v1.1.0 (INTERIM: explicit meta with legacy fallback until backfill).",
)

# 2) locale legacy fallback (insert before `return '';` in fyzsxnb_home_post_locale)
locale_anchor = """	if ( has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post_id ) ) {
		return 'ru-RU';
	}
	return '';
}"""
locale_new = """	if ( has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post_id ) ) {
		return 'ru-RU';
	}
	// ---- INTERIM v1.1.0 legacy fallback (removed in v1.2.0) ----
	// Byte-identical to v1.0.0 so the homepage is unchanged until the explicit
	// meta backfill completes. Exercised only for posts without meta.
	$title = wp_strip_all_tags( get_the_title( $post_id ) );
	if ( preg_match( '/[\\x{0400}-\\x{04FF}]/u', $title ) ) {
		return 'ru-RU';
	}
	if ( preg_match( '/[\\x{3400}-\\x{9FFF}]/u', $title ) ) {
		return '';
	}
	return preg_match( '/[A-Za-z]/', $title ) ? 'en-US' : '';
}"""
assert src.count(locale_anchor) == 1, "locale anchor not unique"
src = src.replace(locale_anchor, locale_new)

# 3) guide legacy fallback (append after the meta check in fyzsxnb_home_is_guide)
guide_anchor = """function fyzsxnb_home_is_guide( $post_id, $locale ) {
	return 'guide' === strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
}"""
guide_new = """function fyzsxnb_home_is_guide( $post_id, $locale ) {
	$kind = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
	if ( 'guide' === $kind ) {
		return true;
	}
	// ---- INTERIM v1.1.0 legacy fallback (removed in v1.2.0) ----
	$haystack = strtolower( get_post_field( 'post_name', $post_id ) . ' ' . wp_strip_all_tags( get_the_title( $post_id ) ) );
	if ( 'ru-RU' === $locale ) {
		return (bool) preg_match( '/(guide|check|repair|verification|гайд|руковод|провер|ремонт|совместим|выбор|ввоз|утильсбор)/u', $haystack );
	}
	return (bool) preg_match( '/(guide|checklist|readiness|procurement|verification|decision-map|how-to|compared)/', $haystack );
}"""
assert src.count(guide_anchor) == 1, "guide anchor not unique"
src = src.replace(guide_anchor, guide_new)

os.makedirs(DST_DIR, exist_ok=True)
with open(DST, "w", encoding="utf-8", newline="\n") as fh:
    fh.write(src)
print("interim written:", DST, os.path.getsize(DST), "bytes")
