<?php
/**
 * Plugin Name: FYZSXNB Home Dynamic Feeds
 * Description: Language-explicit, type-explicit homepage feeds (signals/guides)
 *              with locale+type+query-version keyed cache, precise invalidation,
 *              a QA decision trace and QA-only REST endpoints.
 *              UI V2 0.3.6 Feed Hardening — v1.2.0 (explicit-only decision path).
 * Version: 1.2.0
 * Author: FYZSXNB Engineering
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * ---------------------------------------------------------------------------
 * 0.3.6 contract (content data layer governance)
 *
 * Feed candidates are chosen ONLY from explicit signals:
 *   - locale: post meta `_fyz_content_language` (en|ru|en-us|en-gb|ru-ru).
 *             Secondary STRUCTURAL confirm: category 54 (Russian Library) marks
 *             a post Russian per the site language contract. Title/slug
 *             sniffing has been REMOVED from the decision path.
 *   - type:   post meta `_fyz_content_kind === 'guide'` (stable field).
 *
 * Content without a confirmed locale NEVER enters a homepage feed; it is
 * reported by the QA decision trace as `locale_unknown` and lands in the
 * editorial audit list instead.
 *
 * Shortage policy: feeds shrink to the actual eligible count. There is NO
 * cross-locale fallback and NO stale-snapshot backfill anywhere in this file.
 *
 * Cache: one transient per (locale,type) keyed with the query version
 *   fyzsxnb_home_feed_{locale}_{type}_{FYZSXNB_FEED_QUERY_VERSION}
 * which stores the ordered eligible candidate post ids. Callers apply their
 * own exclude + limit on top, so shrink semantics are preserved exactly.
 *
 * Invalidation: publish / update / delete / trash / restore, category-term
 * changes and `_fyz_content_language` / `_fyz_content_kind` meta changes all
 * clear the affected keys (never the whole site cache).
 * ---------------------------------------------------------------------------
 */

define( 'FYZSXNB_FEED_QUERY_VERSION', 'h3' );
define( 'FYZSXNB_FEED_CACHE_TTL', 15 * MINUTE_IN_SECONDS );
define( 'FYZSXNB_FEED_FETCH_LIMIT', 80 );
define( 'FYZSXNB_FEED_RU_LIBRARY_CAT', 54 );

/* ---------------------------------------------------------------------------
 * Explicit meta registration (REST-visible so the migration tool can write it)
 * ------------------------------------------------------------------------- */
function fyzsxnb_feed_register_meta() {
	$auth = function () {
		return current_user_can( 'edit_posts' );
	};
	register_post_meta(
		'post',
		'_fyz_content_language',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'fyzsxnb_feed_sanitize_language',
			'auth_callback'     => $auth,
		)
	);
	register_post_meta(
		'post',
		'_fyz_content_kind',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'fyzsxnb_feed_sanitize_kind',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'fyzsxnb_feed_register_meta' );

function fyzsxnb_feed_sanitize_language( $value ) {
	$v = strtolower( trim( (string) $value ) );
	if ( in_array( $v, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		return 'en';
	}
	if ( in_array( $v, array( 'ru', 'ru-ru' ), true ) ) {
		return 'ru';
	}
	return '';
}

function fyzsxnb_feed_sanitize_kind( $value ) {
	return 'guide' === strtolower( trim( (string) $value ) ) ? 'guide' : '';
}

/* ---------------------------------------------------------------------------
 * Explicit decision functions (v1.2.0: no heuristic fallback)
 * ------------------------------------------------------------------------- */
function fyzsxnb_home_post_locale( $post_id ) {
	$declared = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_language', true ) ) );
	if ( in_array( $declared, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		return 'en-US';
	}
	if ( in_array( $declared, array( 'ru', 'ru-ru' ), true ) ) {
		return 'ru-RU';
	}
	// Structural confirm per the language contract: RU Library category.
	if ( has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post_id ) ) {
		return 'ru-RU';
	}
	return '';
}

function fyzsxnb_home_is_guide( $post_id, $locale ) {
	return 'guide' === strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
}

/* ---------------------------------------------------------------------------
 * Cache layer (locale + type + query version)
 * ------------------------------------------------------------------------- */
function fyzsxnb_feed_cache_key( $locale, $type ) {
	return 'fyzsxnb_home_feed_' . $locale . '_' . $type . '_' . FYZSXNB_FEED_QUERY_VERSION;
}

function fyzsxnb_feed_get_cached( $locale, $type, &$meta ) {
	$key  = fyzsxnb_feed_cache_key( $locale, $type );
	$data = get_transient( $key );
	if ( is_array( $data ) && isset( $data['ids'], $data['cached_at'] ) ) {
		$meta = $data;
		return $data['ids'];
	}
	$meta = null;
	return null;
}

function fyzsxnb_feed_set_cached( $locale, $type, $ids ) {
	set_transient(
		fyzsxnb_feed_cache_key( $locale, $type ),
		array(
			'ids'           => $ids,
			'cached_at'     => time(),
			'query_version' => FYZSXNB_FEED_QUERY_VERSION,
			'locale'        => $locale,
			'type'          => $type,
		),
		FYZSXNB_FEED_CACHE_TTL
	);
}

function fyzsxnb_feed_build_candidates( $locale, $type ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => FYZSXNB_FEED_FETCH_LIMIT,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
		)
	);
	$ids = array();
	foreach ( $query->posts as $candidate ) {
		$id = (int) $candidate->ID;
		if ( $locale !== fyzsxnb_home_post_locale( $id ) ) {
			continue;
		}
		if ( 'guides' === $type && ! fyzsxnb_home_is_guide( $id, $locale ) ) {
			continue;
		}
		$ids[] = $id;
	}
	return $ids;
}

/* ---------------------------------------------------------------------------
 * Public data API (signature kept identical to v1.0.0)
 * ------------------------------------------------------------------------- */
function fyzsxnb_get_home_feed_posts( $locale, $type, $limit, $exclude = array() ) {
	$locale  = ( 'ru-RU' === $locale ) ? 'ru-RU' : 'en-US';
	$type    = ( 'guides' === $type ) ? 'guides' : 'signals';
	$limit   = max( 0, (int) $limit );
	$exclude = array_map( 'absint', (array) $exclude );

	$cached = fyzsxnb_feed_get_cached( $locale, $type, $meta );
	if ( null === $cached ) {
		$cached = fyzsxnb_feed_build_candidates( $locale, $type );
		fyzsxnb_feed_set_cached( $locale, $type, $cached );
	}

	$posts = array();
	foreach ( $cached as $id ) {
		if ( in_array( (int) $id, $exclude, true ) ) {
			continue;
		}
		$p = get_post( $id );
		if ( $p && 'publish' === $p->post_status ) {
			$posts[] = $p;
		}
		if ( count( $posts ) >= $limit ) {
			break;
		}
	}
	return $posts; // natural shrink: fewer eligible -> fewer results
}

function fyzsxnb_render_home_feed( $posts, $locale, $type ) {
	$is_ru      = 'ru-RU' === $locale;
	$grid_class = 'signals' === $type ? 'fyz-signal-grid' : 'fyz-guide-grid';
	$card_class = 'signals' === $type ? 'fyz-signal' : 'fyz-guide';
	$label      = $is_ru ? 'RU' : 'EN';
	$read_label = $is_ru ? 'Читать' : 'Read';
	ob_start();
	?>
	<div class="<?php echo esc_attr( $grid_class ); ?>" data-fyz-dynamic-feed="<?php echo esc_attr( $type ); ?>" data-fyz-locale="<?php echo esc_attr( $locale ); ?>">
		<?php foreach ( $posts as $feed_post ) : ?>
			<?php
			$post_id = (int) $feed_post->ID;
			$excerpt = wp_trim_words( get_the_excerpt( $post_id ), 'signals' === $type ? 22 : 18, '&hellip;' );
			?>
			<article class="<?php echo esc_attr( $card_class ); ?>">
				<span class="fyz-meta"><?php echo esc_html( $label . ' · ' . get_the_date( 'j M Y', $post_id ) ); ?></span>
				<h3><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
				<?php if ( '' !== trim( $excerpt ) ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
				<?php if ( 'signals' === $type ) : ?><a class="fyz-arrow" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $read_label ); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/* Dormant marker path kept for safety: renders the actual count, no minimum
 * gate, no cross-locale fill (markers no longer exist in page content). */
function fyzsxnb_replace_home_feed_markers( $content ) {
	if ( is_admin() || ! is_singular( 'page' ) || ! in_array( (int) get_queried_object_id(), array( 11, 400 ), true ) ) {
		return $content;
	}
	$pattern = '/<!--\s*fyzsxnb-home-feed:start\s+locale=(en-US|ru-RU)\s+type=(signals|guides)\s*-->(.*?)<!--\s*fyzsxnb-home-feed:end\s*-->/s';
	return preg_replace_callback(
		$pattern,
		function ( $matches ) {
			$locale  = $matches[1];
			$type    = $matches[2];
			$limit   = 'signals' === $type ? 4 : 6;
			$exclude = array();
			if ( 'guides' === $type ) {
				$exclude = wp_list_pluck( fyzsxnb_get_home_feed_posts( $locale, 'signals', 4 ), 'ID' );
			}
			$posts = fyzsxnb_get_home_feed_posts( $locale, $type, $limit, $exclude );
			return empty( $posts ) ? $matches[0] : fyzsxnb_render_home_feed( $posts, $locale, $type );
		},
		$content
	);
}
add_filter( 'the_content', 'fyzsxnb_replace_home_feed_markers', 20 );

/* ---------------------------------------------------------------------------
 * Invalidation (precise: only the affected locale/type keys)
 * ------------------------------------------------------------------------- */
function fyzsxnb_feed_invalidate( $locales, $types = array( 'signals', 'guides' ) ) {
	foreach ( array_unique( (array) $locales ) as $locale ) {
		foreach ( (array) $types as $type ) {
			delete_transient( fyzsxnb_feed_cache_key( $locale, $type ) );
		}
	}
}

function fyzsxnb_feed_purge_pages() {
	foreach ( array( home_url( '/' ), home_url( '/ru/' ) ) as $url ) {
		do_action( 'litespeed_purge_url', $url );
		if ( function_exists( 'rocket_clean_files' ) ) {
			rocket_clean_files( array( $url ) );
		}
		if ( function_exists( 'w3tc_flush_url' ) ) {
			w3tc_flush_url( $url );
		}
	}
}

function fyzsxnb_feed_invalidate_for_post( $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	$locales = array();
	$loc     = fyzsxnb_home_post_locale( $post_id );
	if ( '' !== $loc ) {
		$locales[] = $loc;
	}
	if ( has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post_id ) ) {
		$locales[] = 'ru-RU';
	}
	if ( empty( $locales ) ) {
		// Unknown-locale posts are not in feeds, but a locale may have just
		// become unknown (e.g. RU Library removed): clear both to be safe.
		$locales = array( 'en-US', 'ru-RU' );
	}
	fyzsxnb_feed_invalidate( $locales );
	fyzsxnb_feed_purge_pages();
	do_action( 'fyzsxnb_homepage_feed_cache_purged', $post_id );
}

function fyzsxnb_feed_on_post_saved( $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	fyzsxnb_feed_invalidate_for_post( $post_id );
}
add_action( 'save_post_post', 'fyzsxnb_feed_on_post_saved', 20, 1 );
add_action( 'trashed_post', 'fyzsxnb_feed_invalidate_for_post', 20, 1 );
add_action( 'untrashed_post', 'fyzsxnb_feed_invalidate_for_post', 20, 1 );
add_action( 'before_delete_post', 'fyzsxnb_feed_invalidate_for_post', 20, 1 );

function fyzsxnb_feed_on_meta_changed( $meta_id, $post_id, $meta_key ) {
	if ( ! in_array( $meta_key, array( '_fyz_content_language', '_fyz_content_kind' ), true ) ) {
		return;
	}
	// Old locale cannot be read here (meta already written); these two keys are
	// the locale/type contract, so clear both locales — still precise (only
	// fires on these keys, never on unrelated save_post).
	fyzsxnb_feed_invalidate( array( 'en-US', 'ru-RU' ) );
	fyzsxnb_feed_purge_pages();
}
add_action( 'added_post_meta', 'fyzsxnb_feed_on_meta_changed', 20, 3 );
add_action( 'updated_post_meta', 'fyzsxnb_feed_on_meta_changed', 20, 3 );
add_action( 'deleted_post_meta', 'fyzsxnb_feed_on_meta_changed', 20, 3 );

function fyzsxnb_feed_on_terms_changed( $object_id, $terms, $tt_ids, $taxonomy ) {
	if ( 'category' !== $taxonomy || 'post' !== get_post_type( $object_id ) ) {
		return;
	}
	fyzsxnb_feed_invalidate_for_post( $object_id );
}
add_action( 'set_object_terms', 'fyzsxnb_feed_on_terms_changed', 20, 4 );

/* ---------------------------------------------------------------------------
 * QA decision trace + QA-only REST endpoints (not user-facing)
 * ------------------------------------------------------------------------- */
function fyzsxnb_feed_decision_trace( $post_ids ) {
	$out = array();
	foreach ( array_map( 'absint', (array) $post_ids ) as $pid ) {
		if ( ! $pid ) {
			continue;
		}
		$loc      = fyzsxnb_home_post_locale( $pid );
		$is_guide = fyzsxnb_home_is_guide( $pid, $loc );
		$entry    = array(
			'post_id'   => $pid,
			'locale'    => $loc,
			'feed_type' => array(),
			'eligible'  => false,
			'reason'    => '',
		);
		if ( '' === $loc ) {
			$entry['reason'] = 'locale_unknown';
		} else {
			$entry['feed_type'] = array( 'signals' );
			if ( $is_guide ) {
				$entry['feed_type'][] = 'guides';
			}
			$entry['eligible'] = true;
			$entry['reason']   = $is_guide ? 'explicit_locale+kind_guide' : 'explicit_locale';
		}
		$out[] = $entry;
	}
	return $out;
}

function fyzsxnb_feed_rest_state( WP_REST_Request $request ) {
	$state   = array();
	$exclude = array_map( 'absint', explode( ',', (string) $request->get_param( 'exclude' ) ) );
	$limit   = (int) $request->get_param( 'limit' ); // 0 = report full candidate set
	foreach ( array( 'en-US', 'ru-RU' ) as $locale ) {
		foreach ( array( 'signals', 'guides' ) as $type ) {
			$key      = fyzsxnb_feed_cache_key( $locale, $type );
			$data     = get_transient( $key );
			$cached   = is_array( $data ) && isset( $data['ids'], $data['cached_at'] ) ? $data : null;
			$ids_full = is_array( $cached ) ? $cached['ids'] : fyzsxnb_feed_build_candidates( $locale, $type );
			$ids_eff  = array_values( array_filter( $ids_full, function ( $id ) use ( $exclude ) {
				return ! in_array( (int) $id, $exclude, true );
			} ) );
			if ( $limit > 0 ) {
				$ids_eff = array_slice( $ids_eff, 0, $limit );
			}
			$state[ $locale ][ $type ] = array(
				'cache_key'   => $key,
				'cached'      => null !== $cached,
				'cached_at'   => $cached ? $cached['cached_at'] : null,
				'age_seconds' => $cached ? max( 0, time() - (int) $cached['cached_at'] ) : null,
				'query_version' => FYZSXNB_FEED_QUERY_VERSION,
				'candidate_count' => count( $ids_full ),
				'effective_count' => count( $ids_eff ),
				'effective_ids'   => $ids_eff,
			);
		}
	}
	return rest_ensure_response( $state );
}

function fyzsxnb_feed_rest_trace( WP_REST_Request $request ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $request->get_param( 'ids' ) ) ) );
	return rest_ensure_response( fyzsxnb_feed_decision_trace( $ids ) );
}

function fyzsxnb_feed_rest_cache_delete() {
	$keys = array();
	foreach ( array( 'en-US', 'ru-RU' ) as $locale ) {
		foreach ( array( 'signals', 'guides' ) as $type ) {
			$keys[] = fyzsxnb_feed_cache_key( $locale, $type );
		}
	}
	foreach ( $keys as $key ) {
		delete_transient( $key );
	}
	fyzsxnb_feed_purge_pages();
	return rest_ensure_response( array( 'deleted_keys' => $keys, 'purged_pages' => array( home_url( '/' ), home_url( '/ru/' ) ) ) );
}

add_action(
	'rest_api_init',
	function () {
		$qa = function () {
			return current_user_can( 'edit_posts' );
		};
		register_rest_route(
			'fyzsxnb/v1',
			'/feed-state',
			array(
				'methods'             => 'GET',
				'permission_callback' => $qa,
				'callback'            => 'fyzsxnb_feed_rest_state',
				'args'                => array(
					'exclude' => array( 'type' => 'string', 'required' => false ),
					'limit'   => array( 'type' => 'integer', 'required' => false, 'default' => 0 ),
				),
			)
		);
		register_rest_route(
			'fyzsxnb/v1',
			'/feed-trace',
			array(
				'methods'             => 'GET',
				'permission_callback' => $qa,
				'callback'            => 'fyzsxnb_feed_rest_trace',
				'args'                => array(
					'ids' => array( 'type' => 'string', 'required' => true ),
				),
			)
		);
		register_rest_route(
			'fyzsxnb/v1',
			'/feed-cache',
			array(
				'methods'             => 'DELETE',
				'permission_callback' => $qa,
				'callback'            => 'fyzsxnb_feed_rest_cache_delete',
			)
		);
	}
);
