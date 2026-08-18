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
 * Load the V2 presentation layer without touching content or query logic.
 */
function fyzsxnb_enqueue_research_wire_assets() {
	$css_relative = '/assets/css/research-wire.css';
	$css_absolute = get_stylesheet_directory() . $css_relative;
	$css_version  = file_exists( $css_absolute ) ? (string) filemtime( $css_absolute ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'fyzsxnb-research-wire',
		get_stylesheet_directory_uri() . $css_relative,
		array( 'fyzsxnb-design-system' ),
		$css_version
	);

	if ( is_singular( 'post' ) ) {
		$js_relative = '/assets/js/research-wire.js';
		$js_absolute = get_stylesheet_directory() . $js_relative;
		$js_version  = file_exists( $js_absolute ) ? (string) filemtime( $js_absolute ) : wp_get_theme()->get( 'Version' );

		wp_enqueue_script(
			'fyzsxnb-research-wire',
			get_stylesheet_directory_uri() . $js_relative,
			array(),
			$js_version,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fyzsxnb_enqueue_research_wire_assets', 21 );

/**
 * Add stable scope classes without changing Neve's template hierarchy.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function fyzsxnb_design_body_classes( $classes ) {
	$classes[] = 'fyz-design-system';
	$classes[] = 'fyz-wire-desk';
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
	echo '<p class="fyz-eyebrow">' . esc_html__( 'Research archive', 'fyzsxnb-neve-child' ) . '</p>';
	echo '<h1>' . wp_kses_post( $title ) . '</h1>';

	if ( $description ) {
		echo '<div class="fyz-archive-description">' . wp_kses_post( $description ) . '</div>';
	}

	echo '</header>';
}
add_action( 'neve_before_loop', 'fyzsxnb_archive_intro', 5 );
