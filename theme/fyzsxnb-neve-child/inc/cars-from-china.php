<?php
/**
 * Cars from China — content architecture for the FYZSXNB Research Wire.
 *
 * Scope (FYZSXNB-CARS-FROM-CHINA-MATRIX-V1):
 *   - Two lightweight taxonomies: fyz_vehicle (hierarchical: Brand > Model)
 *     and fyz_research_type (overview / owner-cases / common-problems /
 *     parts-compatibility / market-version / repair-guide / case-study).
 *   - Hierarchical public URLs:
 *       EN: /cars-from-china/{brand}/ /cars-from-china/{brand}/{model}/
 *       RU: /ru/cars-from-china/{brand}/ /ru/cars-from-china/{brand}/{model}/
 *   - Language contract: RU views query Russian Library (category 54) only;
 *     EN views exclude it. Reuses existing mu-plugin language detection.
 *   - Evidence-first: this file scaffolds structure only. It never fabricates
 *     cases, common problems, counts, or parts conclusions, and it never
 *     renders an empty section.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FYZSXNB_CFC_VERSION', '0.1.0' );
define( 'FYZSXNB_CFC_CATEGORY_RU', 54 ); // Russian Library (existing language category).

/**
 * Purge stale route/page caches once for each deployed CFC version.
 */
function fyzsxnb_cfc_purge_cache_once() {
	$option_key = 'fyzsxnb_cfc_cache_version';
	if ( get_option( $option_key ) === FYZSXNB_CFC_VERSION ) {
		return;
	}

	do_action( 'litespeed_purge_all' );
	wp_cache_flush();
	update_option( $option_key, FYZSXNB_CFC_VERSION, false );
}
add_action( 'init', 'fyzsxnb_cfc_purge_cache_once', 100 );

/* -------------------------------------------------------------------------
 * 1. Taxonomies
 * ---------------------------------------------------------------------- */

/**
 * Vehicle taxonomy: Brand (parent) > Model (child).
 * Public but with custom rewrite (handled below) so URLs stay hierarchical.
 */
function fyzsxnb_cfc_register_taxonomies() {
	register_taxonomy(
		'fyz_vehicle',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'          => 'Cars from China — Vehicles',
				'singular_name' => 'Vehicle',
				'menu_name'     => 'Cars from China',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false, // URLs are handled by fyzsxnb_cfc_rewrites().
			'query_var'         => 'fyz_vehicle',
		)
	);

	register_taxonomy(
		'fyz_research_type',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'          => 'Research Type',
				'singular_name' => 'Research Type',
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
			'query_var'         => 'fyz_research_type',
		)
	);
}
add_action( 'init', 'fyzsxnb_cfc_register_taxonomies' );

/**
 * Allowed V1 research types. These are article-type labels, not fault types.
 * Specific fault types must wait for real cases (evidence-first rule).
 */
function fyzsxnb_cfc_research_types() {
	return array(
		'overview'           => array(
			'en' => 'Overview',
			'ru' => 'Обзор',
		),
		'owner-cases'        => array(
			'en' => 'Owner Cases',
			'ru' => 'Опыт владельцев',
		),
		'common-problems'    => array(
			'en' => 'Common Problems',
			'ru' => 'Типовые проблемы',
		),
		'parts-compatibility' => array(
			'en' => 'Parts & Compatibility',
			'ru' => 'Запчасти и совместимость',
		),
		'market-version'     => array(
			'en' => 'China vs Export Version',
			'ru' => 'Китайская vs экспортная версия',
		),
		'repair-guide'       => array(
			'en' => 'Repair Guides',
			'ru' => 'Руководства по ремонту',
		),
		'case-study'         => array(
			'en' => 'Case Studies',
			'ru' => 'Кейсы',
		),
	);
}

/**
 * V1 initial vehicle matrix (brand slug => [model slugs]).
 * This is structural coverage only — no pages are created for empty slots.
 */
function fyzsxnb_cfc_initial_matrix() {
	return array(
		'volkswagen' => array( 'tayron', 'tharu', 'golf', 't-roc' ),
		'audi'       => array( 'q3', 'a3' ),
		'toyota'     => array( 'corolla' ),
		'hyundai'    => array( 'elantra' ),
		'honda'      => array( 'vezel' ),
		'bmw'        => array( 'x1' ),
	);
}

/**
 * Seed the initial vehicle matrix once (option-guarded, idempotent).
 * Brands become parent terms; models become children.
 */
function fyzsxnb_cfc_seed_terms() {
	if ( ! get_option( 'fyzsxnb_cfc_terms_seeded' ) ) {
		foreach ( fyzsxnb_cfc_initial_matrix() as $brand => $models ) {
			$brand_name = ucwords( str_replace( array( '-', '_' ), ' ', $brand ) );
			$parent     = term_exists( $brand, 'fyz_vehicle' );
			if ( ! $parent ) {
				$parent = wp_insert_term( $brand_name, 'fyz_vehicle', array( 'slug' => $brand ) );
			}
			if ( is_wp_error( $parent ) ) {
				continue;
			}
			$parent_id = is_array( $parent ) ? (int) $parent['term_id'] : (int) $parent;

			foreach ( $models as $model ) {
				$model_name = strtoupper( str_replace( array( '-', '_' ), ' ', $model ) );
				if ( 't-roc' === $model ) {
					$model_name = 'T-Roc';
				}
				$existing = term_exists( $model, 'fyz_vehicle' );
				if ( $existing ) {
					$existing_id = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
					$cur         = get_term( $existing_id, 'fyz_vehicle' );
					if ( $cur && (int) $cur->parent !== $parent_id ) {
						wp_update_term( $existing_id, 'fyz_vehicle', array( 'parent' => $parent_id ) );
					}
					continue;
				}
				wp_insert_term( $model_name, 'fyz_vehicle', array( 'slug' => $model, 'parent' => $parent_id ) );
			}
		}

		update_option( 'fyzsxnb_cfc_terms_seeded', 1, false );
	}

	if ( ! get_option( 'fyzsxnb_cfc_research_types_seeded' ) ) {
		$seeded = true;
		foreach ( fyzsxnb_cfc_research_types() as $slug => $labels ) {
			if ( term_exists( $slug, 'fyz_research_type' ) ) {
				continue;
			}
			$inserted = wp_insert_term( $labels['en'], 'fyz_research_type', array( 'slug' => $slug ) );
			if ( is_wp_error( $inserted ) ) {
				$seeded = false;
			}
		}
		if ( $seeded ) {
			update_option( 'fyzsxnb_cfc_research_types_seeded', 1, false );
		}
	}
}
add_action( 'init', 'fyzsxnb_cfc_seed_terms', 11 );

/* -------------------------------------------------------------------------
 * 2. Hierarchical URLs + language prefix
 * ---------------------------------------------------------------------- */

/**
 * Register public query vars used by the Cars from China rewrites.
 */
function fyzsxnb_cfc_query_vars( $vars ) {
	$vars[] = 'fyz_cfc_parent';
	$vars[] = 'fyz_cfc_lang';
	$vars[] = 'fyz_cfc_ru_hub';
	return $vars;
}
add_filter( 'query_vars', 'fyzsxnb_cfc_query_vars' );

/**
 * Rewrite rules. RU prefix first so the more specific rules win.
 *   /cars-from-china/{brand}/{model}/      (EN model)
 *   /cars-from-china/{brand}/              (EN brand)
 *   /ru/cars-from-china/                   (RU hub — no page needed)
 *   /ru/cars-from-china/{brand}/{model}/   (RU model)
 *   /ru/cars-from-china/{brand}/           (RU brand)
 */
function fyzsxnb_cfc_rewrites() {
	add_rewrite_rule( '^ru/cars-from-china/([^/]+)/([^/]+)/?$', 'index.php?fyz_vehicle=$matches[2]&fyz_cfc_parent=$matches[1]&fyz_cfc_lang=ru', 'top' );
	add_rewrite_rule( '^ru/cars-from-china/([^/]+)/?$', 'index.php?fyz_vehicle=$matches[1]&fyz_cfc_lang=ru', 'top' );
	add_rewrite_rule( '^ru/cars-from-china/?$', 'index.php?fyz_cfc_ru_hub=1', 'top' );
	add_rewrite_rule( '^cars-from-china/([^/]+)/([^/]+)/?$', 'index.php?fyz_vehicle=$matches[2]&fyz_cfc_parent=$matches[1]', 'top' );
	add_rewrite_rule( '^cars-from-china/([^/]+)/?$', 'index.php?fyz_vehicle=$matches[1]', 'top' );
}
add_action( 'init', 'fyzsxnb_cfc_rewrites', 12 );

/**
 * Render the RU hub (/ru/cars-from-china/) through the hub page template
 * without requiring a second page object (WP page slugs are globally unique).
 *
 * @param string $template Resolved template path.
 * @return string
 */
function fyzsxnb_cfc_ru_hub_template( $template ) {
	if ( get_query_var( 'fyz_cfc_ru_hub' ) && ! is_admin() ) {
		$hub = get_stylesheet_directory() . '/page-templates/cars-from-china-hub.php';
		if ( file_exists( $hub ) ) {
			return $hub;
		}
	}
	return $template;
}
add_filter( 'template_include', 'fyzsxnb_cfc_ru_hub_template' );

/**
 * Validate the hierarchical URL: when fyz_cfc_parent is set, the queried
 * model term must actually be a child of that brand term. Otherwise 404.
 *
 * @param WP_Query $query The main query.
 */
function fyzsxnb_cfc_validate_parent( $query ) {
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}

	$parent_slug = get_query_var( 'fyz_cfc_parent' );
	$lang        = get_query_var( 'fyz_cfc_lang' );
	if ( ! $parent_slug ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term || ! isset( $term->taxonomy ) || 'fyz_vehicle' !== $term->taxonomy ) {
		$query->set_404();
		return;
	}

	$parent = $term->parent ? get_term( $term->parent, 'fyz_vehicle' ) : null;
	if ( ! $parent || is_wp_error( $parent ) || $parent->slug !== $parent_slug ) {
		$query->set_404();
		return;
	}

	// Language contract: the RU prefix implies the Russian view; the template
	// reads get_query_var('fyz_cfc_lang') via fyzsxnb_cfc_is_ru().
	if ( 'ru' === $lang ) {
		$query->set( 'fyz_cfc_lang', 'ru' );
	}
}
add_action( 'parse_query', 'fyzsxnb_cfc_validate_parent' );

/**
 * Is the current Cars from China view the Russian view?
 * True when the request path is under /ru/ (reuses existing language logic).
 *
 * @return bool
 */
function fyzsxnb_cfc_is_ru() {
	if ( 'ru' === get_query_var( 'fyz_cfc_lang' ) ) {
		return true;
	}
	$path = isset( $_SERVER['REQUEST_URI'] )
		? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH )
		: '';
	return str_starts_with( rtrim( (string) $path, '/' ) . '/', '/ru/' );
}

/**
 * Build the canonical hierarchical URL for a vehicle term in a language.
 *
 * @param WP_Term $term Vehicle term.
 * @param bool    $ru   Russian prefix?
 * @return string
 */
function fyzsxnb_cfc_term_link( $term, $ru = false ) {
	$prefix = $ru ? '/ru' : '';
	if ( $term->parent ) {
		$parent = get_term( $term->parent, 'fyz_vehicle' );
		if ( $parent && ! is_wp_error( $parent ) ) {
			return home_url( $prefix . '/cars-from-china/' . $parent->slug . '/' . $term->slug . '/' );
		}
	}
	return home_url( $prefix . '/cars-from-china/' . $term->slug . '/' );
}

/**
 * Does this model term have at least one published post (any language)?
 * Used by the Model Matrix so unpublished models render as text, not links.
 *
 * @param WP_Term $term Vehicle term.
 * @return bool
 */
function fyzsxnb_cfc_has_published( $term ) {
	$q = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'fyz_vehicle',
					'field'    => 'term_id',
					'terms'    => (int) $term->term_id,
				),
			),
		)
	);
	return (bool) $q->have_posts();
}

/* -------------------------------------------------------------------------
 * 3. Language-aware section queries (evidence-first, empty-safe)
 * ---------------------------------------------------------------------- */

/**
 * Query published posts for a vehicle term, optionally filtered by research
 * type and language. RU views filter to Russian Library (54); EN views
 * exclude it. Returns an array of WP_Post.
 *
 * @param WP_Term $term          Vehicle term.
 * @param string  $research_type Optional fyz_research_type slug.
 * @param bool    $ru            Russian view?
 * @param int     $limit         Max posts.
 * @return WP_Post[]
 */
function fyzsxnb_cfc_posts( $term, $research_type = '', $ru = false, $limit = 6 ) {
	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, min( 20, $limit ) ),
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( $term ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'fyz_vehicle',
			'field'    => 'term_id',
			'terms'    => (int) $term->term_id,
		);
	}

	if ( $research_type ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'fyz_research_type',
			'field'    => 'slug',
			'terms'    => $research_type,
		);
	}

	if ( count( $args['tax_query'] ?? array() ) > 1 ) {
		$args['tax_query']['relation'] = 'AND';
	}

	$args['category__not_in'] = array( FYZSXNB_CFC_CATEGORY_RU ); // EN excludes Russian Library.
	if ( $ru ) {
		unset( $args['category__not_in'] );
		$args['category__in'] = array( FYZSXNB_CFC_CATEGORY_RU ); // RU includes only Russian Library.
	}

	$q = new WP_Query( $args );
	return $q->posts;
}

/* -------------------------------------------------------------------------
 * 4. Active-view detection (for scoped enqueue) + shared render helpers
 * ---------------------------------------------------------------------- */

/**
 * Is the current request part of the Cars from China desk?
 * True on the hub pages, the vehicle taxonomy archive, or any request whose
 * path starts with a cars-from-china segment.
 *
 * @return bool
 */
function fyzsxnb_cfc_is_active_view() {
	if ( is_tax( 'fyz_vehicle' ) ) {
		return true;
	}
	if ( is_page() ) {
		$tmpl = get_page_template_slug( get_queried_object_id() );
		if ( 'page-templates/cars-from-china-hub.php' === $tmpl ) {
			return true;
		}
	}
	$path = isset( $_SERVER['REQUEST_URI'] )
		? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH )
		: '';
	$path = rtrim( (string) $path, '/' );
	return str_contains( $path, '/cars-from-china' );
}

/**
 * Render a post card in the Research Wire list style.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function fyzsxnb_cfc_post_card( $post ) {
	$cats   = get_the_category( $post->ID );
	$labels = array();
	foreach ( (array) $cats as $c ) {
		$labels[] = $c->name;
	}
	$types = wp_get_object_terms( $post->ID, 'fyz_research_type', array( 'fields' => 'names' ) );
	if ( ! is_wp_error( $types ) ) {
		foreach ( (array) $types as $t ) {
			$labels[] = $t;
		}
	}
	$meta = implode( ' · ', array_slice( $labels, 0, 3 ) );
	return sprintf(
		'<li class="cfc-card"><a href="%1$s"><span class="cfc-card__meta">%2$s</span><span class="cfc-card__title">%3$s</span></a></li>',
		esc_url( get_permalink( $post ) ),
		esc_html( $meta ),
		esc_html( get_the_title( $post ) )
	);
}

/**
 * Render a research section on the model page. Returns empty string when the
 * section has no published posts (empty-section suppression).
 *
 * @param string  $type    Research type slug.
 * @param WP_Term $term    Vehicle term.
 * @param bool    $ru      Russian view?
 * @param int     $limit   Max posts.
 * @return string
 */
function fyzsxnb_cfc_section( $type, $term, $ru, $limit = 6 ) {
	$types = fyzsxnb_cfc_research_types();
	if ( ! isset( $types[ $type ] ) ) {
		return '';
	}
	$label = $ru ? $types[ $type ]['ru'] : $types[ $type ]['en'];
	$posts = fyzsxnb_cfc_posts( $term, $type, $ru, $limit );
	if ( empty( $posts ) ) {
		return ''; // Evidence-first: never render an empty box.
	}
	$items = '';
	foreach ( $posts as $p ) {
		$items .= fyzsxnb_cfc_post_card( $p );
	}
	return '<section class="cfc-model-section" id="cfc-' . esc_attr( $type ) . '">'
		. '<h2 class="cfc-section-title">' . esc_html( $label ) . '</h2>'
		. '<ul class="cfc-list">' . $items . '</ul>'
		. '</section>';
}

/* -------------------------------------------------------------------------
 * 5. Model Matrix (reusable component)
 * ---------------------------------------------------------------------- */

/**
 * Render the brand > models matrix. Models with no published research render
 * as plain text; only published model archives become links.
 *
 * @param bool $ru Russian labels?
 * @return string
 */
function fyzsxnb_cfc_model_matrix( $ru = false ) {
	$matrix = fyzsxnb_cfc_initial_matrix();
	$rows   = '';
	foreach ( $matrix as $brand_slug => $model_slugs ) {
		$brand = get_term_by( 'slug', $brand_slug, 'fyz_vehicle' );
		if ( ! $brand ) {
			continue;
		}
		$models = array();
		foreach ( $model_slugs as $m_slug ) {
			$term = get_term_by( 'slug', $m_slug, 'fyz_vehicle' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}
			if ( fyzsxnb_cfc_has_published( $term ) ) {
				$models[] = '<a href="' . esc_url( fyzsxnb_cfc_term_link( $term, $ru ) ) . '">' . esc_html( $term->name ) . '</a>';
			} else {
				$models[] = '<span class="cfc-matrix__pending">' . esc_html( $term->name ) . '</span>';
			}
		}
		$rows .= '<div class="cfc-matrix__row">'
			. '<div class="cfc-matrix__brand">' . esc_html( $brand->name ) . '</div>'
			. '<div class="cfc-matrix__models">' . implode( '<span class="cfc-matrix__sep">·</span>', $models ) . '</div>'
			. '</div>';
	}
	return '<div class="cfc-matrix">' . $rows . '</div>';
}

/* -------------------------------------------------------------------------
 * 6. Hub / Brand / Model page rendering
 * ---------------------------------------------------------------------- */

/**
 * Render the Cars from China hub content (EN or RU).
 *
 * @return string
 */
function fyzsxnb_cfc_render_hub() {
	$ru  = fyzsxnb_cfc_is_ru();
	$out = '';

	// Eyebrow + intro.
	$out .= '<section class="cfc-hero">';
	if ( $ru ) {
		$out .= '<p class="cfc-eyebrow">AUTOMOBILI IZ KITAYA</p>';
		$out .= '<h1 class="cfc-h1">Автомобили китайского рынка в России</h1>';
		$out .= '<p class="cfc-deck">Эксплуатация, ремонт, запчасти, совместимость и параллельный импорт машин, собранных или предназначенных для китайского рынка, — для российских владельцев и покупателей.</p>';
	} else {
		$out .= '<p class="cfc-eyebrow">CARS FROM CHINA</p>';
		$out .= '<h1 class="cfc-h1">China-market vehicles exported worldwide.</h1>';
		$out .= '<p class="cfc-deck">Cars built for the China market — Volkswagen, Audi, Toyota, Hyundai, Honda, BMW and Chinese brands — and what changes when they are used, serviced or imported elsewhere: versions, electronics, ADAS, parts, compatibility and real owner evidence.</p>';
	}
	$out .= '</section>';

	// Model matrix (no dead links).
	$out .= '<section class="cfc-section">'
		. '<h2 class="cfc-section-title">' . ( $ru ? 'Модели' : 'Model Matrix' ) . '</h2>'
		. fyzsxnb_cfc_model_matrix( $ru )
		. '</section>';

	// Latest research (language-filtered).
	$latest = fyzsxnb_cfc_posts( null, '', $ru, 6 );
	if ( $latest ) {
		$items = '';
		foreach ( $latest as $p ) {
			$items .= fyzsxnb_cfc_post_card( $p );
		}
		$out .= '<section class="cfc-section">'
			. '<h2 class="cfc-section-title">' . ( $ru ? 'Последние исследования' : 'Latest Research' ) . '</h2>'
			. '<ul class="cfc-list">' . $items . '</ul>'
			. '</section>';
	}

	// Research areas (static labels only — they are slots, not promises).
	$areas = $ru
		? array( 'Опыт владельцев', 'Запчасти', 'Совместимость', 'Версии рынка', 'Руководства по ремонту' )
		: array( 'Owner Cases', 'Parts', 'Compatibility', 'Market Versions', 'Repair Guides' );
	$area_items = '';
	foreach ( $areas as $a ) {
		$area_items .= '<li>' . esc_html( $a ) . '</li>';
	}
	$out .= '<section class="cfc-section">'
		. '<h2 class="cfc-section-title">' . ( $ru ? 'Направления исследований' : 'Research Areas' ) . '</h2>'
		. '<ul class="cfc-areas">' . $area_items . '</ul>'
		. '</section>';

	// How we research.
	$how = $ru
		? '<p>Доказательная работа: китайские отзывы владельцев, официальные документы, сравнение версий и перекрёстная проверка российскими источниками.</p>'
		: '<p>Evidence-first workflow: Chinese owner reports, official documents, cross-market version comparison, and Russian-side validation.</p>';
	$out .= '<section class="cfc-section">'
		. '<h2 class="cfc-section-title">' . ( $ru ? 'Как мы исследуем' : 'How We Research' ) . '</h2>'
		. $how
		. '</section>';

	// CTA (contact path only — no commerce).
	if ( $ru ) {
		$cta = 'Нужно проверить деталь для автомобиля из Китая?';
	} else {
		$cta = 'Need help identifying a China-market vehicle or part?';
	}
	$out .= '<section class="cfc-cta"><p>' . esc_html( $cta ) . '</p>'
		. '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . ( $ru ? 'Связаться' : 'Contact us' ) . '</a></section>';

	return $out;
}

/**
 * Render a brand page (models covered + latest brand research).
 *
 * @param WP_Term $brand Brand term.
 * @return string
 */
function fyzsxnb_cfc_render_brand( $brand ) {
	$ru  = fyzsxnb_cfc_is_ru();
	$out = '';

	$out .= '<section class="cfc-hero">';
	$out .= '<p class="cfc-eyebrow">CARS FROM CHINA</p>';
	$out .= '<h1 class="cfc-h1">' . esc_html( $brand->name ) . '</h1>';
	$out .= '</section>';

	// Models covered.
	$children = get_terms(
		array(
			'taxonomy'   => 'fyz_vehicle',
			'hide_empty' => false,
			'parent'     => (int) $brand->term_id,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	if ( ! is_wp_error( $children ) && $children ) {
		$items = '';
		foreach ( $children as $child ) {
			if ( fyzsxnb_cfc_has_published( $child ) ) {
				$items .= '<li><a href="' . esc_url( fyzsxnb_cfc_term_link( $child, $ru ) ) . '">' . esc_html( $child->name ) . '</a></li>';
			} else {
				$items .= '<li><span class="cfc-matrix__pending">' . esc_html( $child->name ) . '</span></li>';
			}
		}
		$out .= '<section class="cfc-section">'
			. '<h2 class="cfc-section-title">' . ( $ru ? 'Модели' : 'Models Covered' ) . '</h2>'
			. '<ul class="cfc-areas">' . $items . '</ul>'
			. '</section>';
	}

	// Latest brand research (language-filtered; only if any exists).
	$latest = fyzsxnb_cfc_posts( $brand, '', $ru, 6 );
	if ( $latest ) {
		$items = '';
		foreach ( $latest as $p ) {
			$items .= fyzsxnb_cfc_post_card( $p );
		}
		$out .= '<section class="cfc-section">'
			. '<h2 class="cfc-section-title">' . ( $ru ? 'Последние исследования' : 'Latest Research' ) . '</h2>'
			. '<ul class="cfc-list">' . $items . '</ul>'
			. '</section>';
	}

	return $out;
}

/**
 * Render a model page. Every section auto-populates from vehicle taxonomy +
 * research type + language filter, and empty sections are suppressed.
 *
 * @param WP_Term $model Model term.
 * @return string
 */
function fyzsxnb_cfc_render_model( $model ) {
	$ru  = fyzsxnb_cfc_is_ru();
	$out = '';

	$parent = $model->parent ? get_term( $model->parent, 'fyz_vehicle' ) : null;
	$crumbs = $parent && ! is_wp_error( $parent )
		? '<a href="' . esc_url( fyzsxnb_cfc_term_link( $parent, $ru ) ) . '">' . esc_html( $parent->name ) . '</a>'
		: 'Cars from China';
	$out .= '<nav class="cfc-crumbs">' . $crumbs . ' › <span>' . esc_html( $model->name ) . '</span></nav>';

	$out .= '<section class="cfc-hero">';
	$out .= '<p class="cfc-eyebrow">CARS FROM CHINA</p>';
	$out .= '<h1 class="cfc-h1">' . esc_html( $model->name ) . '</h1>';
	$out .= '<p class="cfc-deck">' . ( $ru ? 'Исследование автомобиля китайского рынка.' : 'China-market vehicle research.' ) . '</p>';
	$out .= '</section>';

	// Latest research (any type).
	$latest = fyzsxnb_cfc_posts( $model, '', $ru, 6 );
	if ( $latest ) {
		$items = '';
		foreach ( $latest as $p ) {
			$items .= fyzsxnb_cfc_post_card( $p );
		}
		$out .= '<section class="cfc-model-section">'
			. '<h2 class="cfc-section-title">' . ( $ru ? 'Последние исследования' : 'Latest Research' ) . '</h2>'
			. '<ul class="cfc-list">' . $items . '</ul>'
			. '</section>';
	}

	// Research-type sections in reading order; empty ones render nothing.
	$order = array( 'owner-cases', 'common-problems', 'parts-compatibility', 'market-version', 'repair-guide', 'case-study' );
	foreach ( $order as $type ) {
		$out .= fyzsxnb_cfc_section( $type, $model, $ru );
	}

	return $out;
}
