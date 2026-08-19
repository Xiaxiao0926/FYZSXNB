<?php
/**
 * FYZSXNB child-theme bootstrap.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preserve the current Neve Customizer settings on first child-theme activation.
 */
function fyzsxnb_inherit_neve_theme_mods() {
	$child_option = 'theme_mods_' . get_stylesheet();
	$child_mods   = get_option( $child_option, array() );

	if ( ! empty( $child_mods ) ) {
		return;
	}

	$parent_mods = get_option( 'theme_mods_neve', array() );

	if ( is_array( $parent_mods ) && ! empty( $parent_mods ) ) {
		update_option( $child_option, $parent_mods );
	}
}
add_action( 'after_switch_theme', 'fyzsxnb_inherit_neve_theme_mods' );

/**
 * Purge generated caches once for each deployed child-theme version.
 */
function fyzsxnb_purge_design_cache_once() {
	$version    = wp_get_theme()->get( 'Version' );
	$option_key = 'fyzsxnb_design_cache_version';

	if ( get_option( $option_key ) === $version ) {
		return;
	}

	do_action( 'litespeed_purge_all' );
	wp_cache_flush();
	update_option( $option_key, $version, false );
}
add_action( 'init', 'fyzsxnb_purge_design_cache_once', 99 );

/**
 * Detect Russian editorial views independently of the site's admin locale.
 *
 * @return bool
 */
function fyzsxnb_is_russian_view() {
	if ( is_page( 'ru' ) || is_category( 'russian-library' ) ) {
		return true;
	}

	if ( is_singular( 'post' ) && has_category( 'russian-library' ) ) {
		return true;
	}

	$path = isset( $_SERVER['REQUEST_URI'] )
		? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH )
		: '';

	return '/ru' === rtrim( $path, '/' ) || str_starts_with( $path, '/ru/' );
}

/**
 * Load the design system after Neve's frontend stylesheet.
 */
function fyzsxnb_enqueue_design_system() {
	$relative_path = '/assets/css/design-system.css';
	$absolute_path = get_stylesheet_directory() . $relative_path;
	$version       = file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'fyzsxnb-design-system',
		get_stylesheet_directory_uri() . $relative_path,
		array( 'neve-style' ),
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'fyzsxnb_enqueue_design_system', 20 );

/**
 * Add stable scope classes without changing Neve's template hierarchy.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function fyzsxnb_design_body_classes( $classes ) {
	$classes[] = 'fyz-design-system';
	$classes[] = fyzsxnb_is_russian_view() ? 'fyz-lang-ru' : 'fyz-lang-en';

	return $classes;
}
add_filter( 'body_class', 'fyzsxnb_design_body_classes' );

/**
 * Replace the public email-based byline with an editorial identity.
 * The WordPress user record is changed separately only after a REST snapshot.
 *
 * @param string   $markup      Existing Neve author markup.
 * @param int|null $post_id     Post ID.
 * @param bool     $show_before Whether to include the byline prefix.
 * @return string
 */
function fyzsxnb_editorial_author_markup( $markup, $post_id, $show_before ) {
	unset( $markup, $post_id );

	$is_russian = fyzsxnb_is_russian_view();
	$label      = $is_russian ? 'Редакция FYZSXNB' : 'FYZSXNB Editorial Desk';
	$prefix     = ( $show_before && ! $is_russian ) ? 'by ' : '';

	return '<span class="author-name fn">' . esc_html( $prefix . $label ) . '</span>';
}
add_filter( 'neve_filter_author_meta_markup', 'fyzsxnb_editorial_author_markup', 10, 3 );

/**
 * Add a readable archive identity when Neve's archive title is disabled.
 */
function fyzsxnb_archive_intro() {
	if ( ! is_archive() ) {
		return;
	}

	$title       = get_the_archive_title();
	$description = get_the_archive_description();

	echo '<header class="fyz-archive-intro">';
	$eyebrow = fyzsxnb_is_russian_view() ? 'Архив исследований' : 'Research archive';
	echo '<p class="fyz-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	echo '<h1>' . wp_kses_post( $title ) . '</h1>';

	if ( $description ) {
		echo '<div class="fyz-archive-description">' . wp_kses_post( $description ) . '</div>';
	}

	echo '</header>';
}
add_action( 'neve_before_loop', 'fyzsxnb_archive_intro', 5 );

/**
 * Cars from China desk — minimal production bootstrap.
 * ONLY the CFC layer is added here; the Research Wire presentation layer
 * (research-wire.css/js) is intentionally NOT part of this deployment.
 * See inc/cars-from-china.php for the full module.
 */
require_once get_stylesheet_directory() . '/inc/cars-from-china.php';

/**
 * Enqueue the Cars from China stylesheet after the design system.
 */
function fyzsxnb_cfc_enqueue_styles() {
	if ( ! fyzsxnb_cfc_is_active_view() ) {
		return;
	}
	$css_relative = '/assets/css/cars-from-china.css';
	$css_absolute = get_stylesheet_directory() . $css_relative;
	$css_version  = file_exists( $css_absolute ) ? (string) filemtime( $css_absolute ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'fyzsxnb-cars-from-china',
		get_stylesheet_directory_uri() . $css_relative,
		array( 'fyzsxnb-design-system' ),
		$css_version
	);
}
add_action( 'wp_enqueue_scripts', 'fyzsxnb_cfc_enqueue_styles', 21 );

/**
 * Self-hosted font faces re-emitted late in <head>.
 * WP core prints its own @font-face set (wp-fonts-local, wp_head priority 50)
 * which currently wins the cascade for 'Inter' and downloads WooCommerce's
 * full 326 KB variable font on every page. Emitting the subsetted faces at
 * priority 999 makes our Latin/Cyrillic subsets win (same family, later
 * declaration). Remains fully self-hosted; no external requests.
 */
function fyzsxnb_self_hosted_fonts_late() {
	$base = get_stylesheet_directory_uri() . '/assets/fonts/';
	echo '<style id="fyzsxnb-fonts-late">' . "
";
	echo "@font-face{font-family:'Inter';font-style:normal;font-weight:400 900;font-display:swap;src:url('{$base}inter-latin.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}
";
	echo "@font-face{font-family:'Inter';font-style:normal;font-weight:400 900;font-display:swap;src:url('{$base}inter-cyrillic.woff2') format('woff2');unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;}
";
	echo "@font-face{font-family:'Noto Serif';font-style:normal;font-weight:400 900;font-display:swap;src:url('{$base}noto-serif-latin.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}
";
	echo "@font-face{font-family:'Noto Serif';font-style:normal;font-weight:400 900;font-display:swap;src:url('{$base}noto-serif-cyrillic.woff2') format('woff2');unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;}
";
	echo '</style>' . "
";
}
add_action( 'wp_head', 'fyzsxnb_self_hosted_fonts_late', 999 );

/**
 * Remove the WooCommerce-registered 'Inter' font face from the WP Fonts
 * queue (wp-fonts-local). It wins the cascade over our subsetted faces and
 * forces a 326 KB full-variable download on every page. Cardo is left alone.
 */
add_action( 'wp_enqueue_scripts', 'fyzsxnb_dequeue_wc_inter_font', PHP_INT_MAX );
function fyzsxnb_dequeue_wc_inter_font() {
	if ( ! function_exists( 'wp_fonts' ) ) {
		return;
	}
	try {
		wp_fonts()->dequeue( 'inter' );
		wp_fonts()->dequeue( 'Inter' );
		wp_fonts()->remove( 'inter' );
		wp_fonts()->remove( 'Inter' );
	} catch ( Throwable $e ) { /* non-fatal */ }
}


/* -------------------------------------------------------------------------
 * UI V2 0.3.4 — Article / Desk / Archive V2
 * ---------------------------------------------------------------------- */

/**
 * Enqueue the Research Wire presentation layer (article TOC + desk styles).
 * The JS is vanilla, deferred, footer-loaded and feature-scoped: it only
 * builds a TOC when a single-post content area exists. It never changes
 * content, headings, URLs or SEO output.
 */
function fyzsxnb_enqueue_research_wire_assets() {
	$css_rel = '/assets/css/research-wire.css';
	$css_abs = get_stylesheet_directory() . $css_rel;
	$css_ver = file_exists( $css_abs ) ? (string) filemtime( $css_abs ) : wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'fyzsxnb-research-wire', get_stylesheet_directory_uri() . $css_rel, array( 'fyzsxnb-design-system' ), $css_ver );

	// research-wire JS is printed inline in the footer (see
	// fyzsxnb_print_toc_inline): this host's LiteSpeed 'load JS deferred'
	// never executes external deferred scripts, so a regular enqueue would
	// not run. Inline printing keeps the single-source file and guarantees
	// the TOC (progressive enhancement) actually runs.
}
add_action( 'wp_enqueue_scripts', 'fyzsxnb_enqueue_research_wire_assets', 21 );

/**
 * Print the research-wire TOC module inline (footer). The file remains the
 * single source of truth; inline output sidesteps LiteSpeed's 'load JS
 * deferred', which on this host never executes external deferred scripts.
 * Runs at end-of-body so the DOM is ready; vanilla, no jQuery, no deps.
 */
function fyzsxnb_print_toc_inline() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$file = get_stylesheet_directory() . '/assets/js/research-wire.js';
	if ( ! file_exists( $file ) ) {
		return;
	}
	$code = file_get_contents( $file );
	if ( false === $code || false !== strpos( $code, '</script' ) ) {
		return;
	}
	echo '<script id="fyzsxnb-toc-inline">' . $code . '</script>';
}
add_action( 'wp_footer', 'fyzsxnb_print_toc_inline', 99 );

/**
 * Article V2 — pre-content block: breadcrumb + desk eyebrow.
 * Rendered above the H1 via grid order in the article layout.
 */
function fyzsxnb_article_shell_pre() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$ru  = fyzsxnb_is_russian_view();
	$cats = get_the_category();
	$cat  = $cats ? $cats[0] : null;

	echo '<div class="fyz-article-top__pre">';
	if ( $cat ) {
		echo '<nav class="fyz-crumbs" aria-label="' . esc_attr( $ru ? 'Хлебные крошки' : 'Breadcrumb' ) . '">';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $ru ? 'Главная' : 'Home' ) . '</a> › ';
		echo '<a href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
		echo '</nav>';
		echo '<p class="fyz-eyebrow">' . esc_html( $cat->name ) . '</p>';
	}
	echo '</div>';
}
add_action( 'neve_before_post_content', 'fyzsxnb_article_shell_pre', 5 );

/**
 * Article V2 — meta row + deck, placed below the H1 in the body column.
 * Only real, existing data is shown (published / updated / research type /
 * editorial byline / language). No invented fields.
 */
function fyzsxnb_article_shell_meta() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$ru = fyzsxnb_is_russian_view();

	echo '<div class="fyz-article-top__meta">';
	echo '<span class="fyz-meta">' . esc_html( $ru ? 'Опубликовано' : 'Published' ) . ': ' . esc_html( fyzsxnb_local_date( get_the_time( 'U' ) ) ) . '</span>';

	$modified = get_the_modified_date();
	if ( $modified && $modified !== get_the_date() ) {
		echo '<span class="fyz-meta">' . esc_html( $ru ? 'Обновлено' : 'Updated' ) . ': ' . esc_html( fyzsxnb_local_date( get_the_modified_time( 'U' ) ) ) . '</span>';
	}

	$types = wp_get_object_terms( get_the_ID(), 'fyz_research_type', array( 'fields' => 'names' ) );
	if ( ! is_wp_error( $types ) && $types ) {
		echo '<span class="fyz-meta">' . esc_html( implode( ', ', array_slice( $types, 0, 2 ) ) ) . '</span>';
	}

	echo '<span class="fyz-meta">' . esc_html( $ru ? 'Редакция FYZSXNB' : 'FYZSXNB Editorial Desk' ) . '</span>';
	echo '<span class="fyz-meta">' . esc_html( $ru ? 'RU' : 'EN' ) . '</span>';
	echo '</div>';

	$deck = get_the_excerpt();
	if ( $deck ) {
		echo '<p class="fyz-article-deck">' . esc_html( wp_strip_all_tags( $deck ) ) . '</p>';
	}
}
add_action( 'neve_before_post_content', 'fyzsxnb_article_shell_meta', 10 );

/**
 * Article V2 — Related research (same locale only, never cross-language;
 * fewer than 3 results render fewer cards) + one Research CTA at the end.
 */
function fyzsxnb_article_shell_after() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$ru      = fyzsxnb_is_russian_view();
	$post_id = get_the_ID();

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);

	if ( $ru ) {
		$args['category__in'] = array( FYZSXNB_CFC_CATEGORY_RU );
	} else {
		$args['category__not_in'] = array( FYZSXNB_CFC_CATEGORY_RU );
	}

	$vehicle = wp_get_post_terms( $post_id, 'fyz_vehicle', array( 'fields' => 'ids' ) );
	if ( ! is_wp_error( $vehicle ) && $vehicle ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'fyz_vehicle',
			'field'    => 'term_id',
			'terms'    => $vehicle,
		);
	} else {
		$cats = wp_get_post_categories( $post_id );
		if ( $cats ) {
			$args['category__in'] = $cats;
		}
	}

	$related = new WP_Query( $args );
	if ( $related->have_posts() ) {
		echo '<section class="fyz-related">';
		echo '<h2 class="fyz-section-title">' . esc_html( $ru ? 'Похожие исследования' : 'Related Research' ) . '</h2>';
		echo '<ul class="fyz-related__list">';
		while ( $related->have_posts() ) {
			$related->the_post();
			$c = get_the_category();
			$l = $c ? $c[0]->name : '';
			echo '<li class="fyz-related__item"><a href="' . esc_url( get_permalink() ) . '">'
				. '<span class="fyz-related__meta">' . esc_html( $l ) . ' · ' . esc_html( fyzsxnb_local_date( get_the_time( 'U' ) ) ) . '</span>'
				. '<span class="fyz-related__title">' . esc_html( get_the_title() ) . '</span>'
				. '</a></li>';
		}
		wp_reset_postdata();
		echo '</ul></section>';
	}

	echo '<section class="fyz-research-cta">';
	echo '<p class="fyz-research-cta__title">' . esc_html( $ru ? 'Нужно проверить деталь, модель или поставщика?' : 'Need to verify a part, model or supplier?' ) . '</p>';
	echo '<p class="fyz-research-cta__copy">' . esc_html( $ru ? 'Пришлите номер детали, фото или документацию.' : 'Send the model number, photos or documentation.' ) . '</p>';
	echo '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html( $ru ? 'Связаться' : 'Contact us' ) . '</a>';
	echo '</section>';
}
add_action( 'neve_after_post_content', 'fyzsxnb_article_shell_after', 10 );


/* -------------------------------------------------------------------------
 * UI V2 0.3.4.1 — legacy cleanup (comments off, footer brand, RU dates,
 * archive language isolation). No visual redesign.
 * ---------------------------------------------------------------------- */

/**
 * 1) Templates layer: research articles render no comment form and no
 * legacy comment strings. Historic comment rows are left in the DB.
 */
function fyzsxnb_comments_template_off( $file ) {
	if ( is_singular( 'post' ) ) {
		$off = get_stylesheet_directory() . '/comments-disabled.php';
		if ( file_exists( $off ) ) {
			return $off;
		}
	}
	return $file;
}
add_filter( 'comments_template', 'fyzsxnb_comments_template_off' );

function fyzsxnb_comments_open_off() {
	if ( is_singular( 'post' ) ) {
		return false;
	}
	return true;
}
add_filter( 'comments_open', 'fyzsxnb_comments_open_off' );

/**
 * 2) Footer credit -> FYZSXNB unified brand (no "Neve | Powered by WordPress").
 * Overrides the theme_mod rendered by Neve's footer builder; no IA change.
 */
function fyzsxnb_footer_credit( $content ) {
	$ru = function_exists( 'fyzsxnb_is_russian_view' ) && fyzsxnb_is_russian_view();
	$year = gmdate( 'Y' );
	unset( $content );
	return '© ' . $year . ' FYZSXNB' . ( $ru ? ' — исследовательский деск' : ' — Research Desk' );
}
add_filter( 'theme_mod_footer_copyright_content', 'fyzsxnb_footer_credit' );

/**
 * 3) RU date locale via a central helper (locale formatting, no per-article
 * hardcoding). EN keeps the site date format.
 */
function fyzsxnb_local_date( $ts ) {
	if ( function_exists( 'fyzsxnb_is_russian_view' ) && fyzsxnb_is_russian_view() ) {
		static $fmt = null;
		static $tried = false;
		if ( ! $tried ) {
			$tried = true;
			if ( class_exists( 'IntlDateFormatter' ) ) {
				try {
					$fmt = new IntlDateFormatter( 'ru_RU', IntlDateFormatter::LONG, IntlDateFormatter::NONE, wp_timezone() );
				} catch ( Throwable $e ) {
					$fmt = false;
				}
			}
		}
		if ( $fmt ) {
			$s = $fmt->format( $ts );
			if ( false !== $s ) {
				return $s;
			}
		}
		// Central fallback map (one helper, not per-article markup).
		$months = array( 1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря' );
		$ts += ( wp_timezone()->getOffset( new DateTime( '@' . $ts ) ) );
		$g = $ts;
		return (string) gmdate( 'j', $g ) . ' ' . $months[ (int) gmdate( 'n', $g ) ] . ' ' . gmdate( 'Y', $g );
	}
	return wp_date( get_option( 'date_format' ), $ts );
}

/**
 * 4) Archive main-query language isolation (theme/query layer; does NOT touch
 * the homepage feeds plugin). EN archives exclude the Russian Library (54);
 * RU archives include only 54. Feeds/admin untouched; fewer results show less.
 */
function fyzsxnb_archive_language_isolation( $query ) {
	if ( is_admin() || is_feed() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! is_archive() ) {
		return;
	}
	$ru = fyzsxnb_is_russian_view();
	if ( $ru ) {
		$query->set( 'category__in', array( FYZSXNB_CFC_CATEGORY_RU ) );
	} else {
		$query->set( 'category__not_in', array( FYZSXNB_CFC_CATEGORY_RU ) );
	}
}
add_action( 'pre_get_posts', 'fyzsxnb_archive_language_isolation' );

