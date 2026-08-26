<?php
/**
 * Plugin Name: FYZSXNB Home Dynamic Feeds
 * Description: Language-explicit, type-explicit homepage feeds (signals/guides)
 *              with locale+type+query-version keyed cache, precise invalidation,
 *              a QA decision trace and QA-only REST endpoints.
 *              UI V2 0.4.5-A — v1.2.5 (Language Contract V2: en/ru/zh support; strict feed isolation).
 * Version: 1.2.5
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
	if ( in_array( $v, array( 'zh', 'zh-cn', 'zh-hans', 'zh_cn', 'zh_hans' ), true ) ) {
		return 'zh';
	}
	return '';
}

function fyzsxnb_feed_sanitize_kind( $value ) {
	$v = strtolower( trim( (string) $value ) );
	return in_array( $v, array( 'signal', 'guide' ), true ) ? $v : '';
}

/* ---------------------------------------------------------------------------
 * Explicit decision functions (v1.2.0: no heuristic fallback; v1.2.5: zh support)
 * ------------------------------------------------------------------------- */
function fyzsxnb_home_post_locale( $post_id ) {
	$declared = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_language', true ) ) );
	if ( in_array( $declared, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		return 'en-US';
	}
	if ( in_array( $declared, array( 'ru', 'ru-ru' ), true ) ) {
		return 'ru-RU';
	}
	if ( in_array( $declared, array( 'zh', 'zh-cn', 'zh-hans', 'zh_cn', 'zh_hans' ), true ) ) {
		return 'zh-CN';
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
			$thumb   = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $post_id ) ) ) ) : '';
			?>
			<article class="<?php echo esc_attr( $card_class ); ?>">
				<?php if ( ! empty( $thumb ) ) : ?>
					<div class="fyz-card__thumb">
						<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo $thumb; ?></a>
					</div>
				<?php endif; ?>
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
	$urls = array( home_url( '/' ), home_url( '/ru/' ) );
	foreach ( array( 'fyzsxnb/v1/feed-state', 'fyzsxnb/v1/feed-trace', 'fyzsxnb/v1/feed-cache' ) as $rest_path ) {
		$urls[] = rest_url( $rest_path );
	}
	foreach ( $urls as $url ) {
		do_action( 'litespeed_purge_url', $url );
		if ( function_exists( 'rocket_clean_files' ) ) {
			rocket_clean_files( array( $url ) );
		}
		if ( function_exists( 'w3tc_flush_url' ) ) {
			w3tc_flush_url( $url );
		}
	}
	// Class-based URL purge — more reliable on LiteSpeed builds where the
	// action-only path is not wired (observed on Hostinger + LSCWP 7.9).
	if ( class_exists( '\LiteSpeed\Purge' ) && method_exists( '\LiteSpeed\Purge', 'purge_url' ) ) {
		try {
			\LiteSpeed\Purge::purge_url( $urls );
		} catch ( \Throwable $e ) {
			// Purge must never break the request.
		}
	}
}

/** QA responses must never be cached (LiteSpeed cached an authed 200 once). */
function fyzsxnb_feed_qa_nocache() {
	nocache_headers();
	if ( ! headers_sent() ) {
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
	}
	do_action( 'litespeed_control_set_nocache', 'fyzsxnb-feed-qa' );
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
	fyzsxnb_feed_qa_nocache();
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
	fyzsxnb_feed_qa_nocache();
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $request->get_param( 'ids' ) ) ) );
	return rest_ensure_response( fyzsxnb_feed_decision_trace( $ids ) );
}

function fyzsxnb_feed_rest_cache_delete() {
	fyzsxnb_feed_qa_nocache();
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

/* ---------------------------------------------------------------------------
 * 0.3.6.1 Publication Metadata Contract
 *
 * Every NEW post entering the publication flow must have its locale and content
 * kind explicitly persisted — no inference from category/slug/title/URL/body.
 *   _fyz_content_language : 'en' | 'ru'        (draft/pending may be empty)
 *   _fyz_content_kind     : 'signal' | 'guide' (draft/pending may be empty)
 * Publishing WITHOUT both fields is blocked: the post is demoted to pending
 * and an admin notice explains why. Hint text may suggest (e.g. "post is in
 * the Russian Library") but NEVER auto-decides. Feed query/cache/trace logic
 * from 0.3.6 is intentionally untouched.
 * ------------------------------------------------------------------------- */

function fyzsxnb_pubmeta_add_meta_box() {
	add_meta_box( 'fyzsxnb-content-metadata', 'FYZSXNB Content Metadata', 'fyzsxnb_pubmeta_render_meta_box', 'post', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'fyzsxnb_pubmeta_add_meta_box' );

function fyzsxnb_pubmeta_render_meta_box( $post ) {
	wp_nonce_field( 'fyzsxnb_pubmeta_save', 'fyzsxnb_pubmeta_nonce' );
	$lang = strtolower( trim( (string) get_post_meta( $post->ID, '_fyz_content_language', true ) ) );
	$kind = strtolower( trim( (string) get_post_meta( $post->ID, '_fyz_content_kind', true ) ) );
	if ( in_array( $lang, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		$lang = 'en';
	} elseif ( in_array( $lang, array( 'ru', 'ru-ru' ), true ) ) {
		$lang = 'ru';
	} elseif ( in_array( $lang, array( 'zh', 'zh-cn', 'zh-hans', 'zh_cn', 'zh_hans' ), true ) ) {
		$lang = 'zh';
	}
	if ( ! in_array( $kind, array( 'signal', 'guide' ), true ) ) {
		$kind = '';
	}
	echo '<p><strong>Language</strong></p>';
	echo '<label><input type="radio" name="fyzsxnb_content_language" value="en"' . checked( $lang, 'en', false ) . '> English</label><br>';
	echo '<label><input type="radio" name="fyzsxnb_content_language" value="ru"' . checked( $lang, 'ru', false ) . '> Russian</label><br>';
	echo '<label><input type="radio" name="fyzsxnb_content_language" value="zh"' . checked( $lang, 'zh', false ) . '> Chinese (zh)</label>';
	echo '<p><strong>Content kind</strong></p>';
	echo '<label><input type="radio" name="fyzsxnb_content_kind" value="signal"' . checked( $kind, 'signal', false ) . '> Signal</label><br>';
	echo '<label><input type="radio" name="fyzsxnb_content_kind" value="guide"' . checked( $kind, 'guide', false ) . '> Guide</label>';
	echo '<p class="description">Required for homepage feed eligibility. Drafts may leave these empty; publishing requires both.</p>';
	// Hints only — never auto-decide (0.3.6 removed heuristics on purpose).
	if ( '' === $lang && has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post->ID ) ) {
		echo '<p class="description" style="color:#b26b00">Hint: this post is in the Russian Library category, but Content language is not set.</p>';
	}
	if ( '' === $lang && preg_match( '/[\x{0400}-\x{04FF}]/u', (string) get_the_title( $post->ID ) ) ) {
		echo '<p class="description" style="color:#b26b00">Hint: the title contains Cyrillic text, but Content language is not set.</p>';
	}
	if ( 'zh' === $lang && has_category( FYZSXNB_FEED_RU_LIBRARY_CAT, $post->ID ) ) {
		echo '<p class="description" style="color:#b32d2e">Warning: Content language is set to Chinese (zh), but post is assigned to Russian Library (category 54). Remove category 54.</p>';
	}
	$missing = get_post_meta( $post->ID, '_fyz_pubmeta_blocks', true );
	if ( $missing ) {
		$list = is_array( $missing ) ? implode( ', ', $missing ) : (string) $missing;
		echo '<p class="description" style="color:#b32d2e">Blocked from publishing: missing ' . esc_html( $list ) . ' — set them and publish again.</p>';
	}
}

function fyzsxnb_pubmeta_save( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || 'post' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['fyzsxnb_pubmeta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fyzsxnb_pubmeta_nonce'] ), 'fyzsxnb_pubmeta_save' ) ) {
		return; // quick edit / bulk edit / REST: never touch meta
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['fyzsxnb_content_language'] ) ) {
		$lang = fyzsxnb_feed_sanitize_language( wp_unslash( $_POST['fyzsxnb_content_language'] ) );
		if ( '' !== $lang ) {
			update_post_meta( $post_id, '_fyz_content_language', $lang );
		}
	}
	if ( isset( $_POST['fyzsxnb_content_kind'] ) ) {
		$kind = fyzsxnb_feed_sanitize_kind( wp_unslash( $_POST['fyzsxnb_content_kind'] ) );
		if ( '' !== $kind ) {
			update_post_meta( $post_id, '_fyz_content_kind', $kind );
		}
	}
	delete_post_meta( $post_id, '_fyz_pubmeta_blocks' );
}
add_action( 'save_post_post', 'fyzsxnb_pubmeta_save', 10, 1 );

/** Block publishing without explicit metadata: demote to pending + notify.
 *  Admin path: runs on save_post AFTER the meta box saved the fields (10).
 *  REST path: skipped here (meta is written by the REST controller AFTER
 *  wp_insert_post, so save_post cannot see it yet); enforced on
 *  rest_after_insert_post instead, where meta is settled. */
function fyzsxnb_pubmeta_enforce( $post_id ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return; // REST create/update: meta is written after save_post
	}
	if ( 'post' !== get_post_type( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return; // draft / pending may stay empty
	}
	$missing = fyzsxnb_pubmeta_missing_fields( $post_id );
	if ( ! $missing ) {
		return;
	}
	fyzsxnb_pubmeta_demote( $post_id, $missing );
}
add_action( 'save_post_post', 'fyzsxnb_pubmeta_enforce', 30, 1 );

/** REST path enforcement — runs after the controller wrote request meta. */
function fyzsxnb_pubmeta_enforce_rest( $post, $request, $creating ) {
	if ( ! $post || 'post' !== get_post_type( $post->ID ) ) {
		return;
	}
	if ( 'publish' !== get_post_status( $post->ID ) ) {
		return;
	}
	$missing = fyzsxnb_pubmeta_missing_fields( $post->ID );
	if ( ! $missing ) {
		return;
	}
	fyzsxnb_pubmeta_demote( $post->ID, $missing );
}
add_action( 'rest_after_insert_post', 'fyzsxnb_pubmeta_enforce_rest', 10, 3 );

function fyzsxnb_pubmeta_missing_fields( $post_id ) {
	$lang = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_language', true ) ) );
	$kind = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
	$missing = array();
	if ( ! in_array( $lang, array( 'en', 'ru', 'zh' ), true ) ) {
		$missing[] = 'language';
	}
	if ( ! in_array( $kind, array( 'signal', 'guide' ), true ) ) {
		$missing[] = 'kind';
	}
	return $missing;
}

function fyzsxnb_pubmeta_demote( $post_id, $missing ) {
	// Demote to pending (safe: no half-published state) and record the reason.
	wp_update_post(
		array(
			'ID'          => $post_id,
			'post_status' => 'pending',
		)
	);
	update_post_meta( $post_id, '_fyz_pubmeta_blocks', $missing );
	set_transient( 'fyzsxnb_pubmeta_notice_' . $post_id, $missing, 5 * MINUTE_IN_SECONDS );
}

function fyzsxnb_pubmeta_admin_notices() {
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	if ( ! $post_id ) {
		return;
	}
	$missing = get_transient( 'fyzsxnb_pubmeta_notice_' . $post_id );
	if ( ! $missing ) {
		return;
	}
	$list = is_array( $missing ) ? implode( ', ', $missing ) : (string) $missing;
	echo '<div class="notice notice-warning"><p>' . esc_html(
		'FYZSXNB Content Metadata: ' . $list . ' is required to publish. The post was kept as Pending. Set Content language and Content kind, then publish again.'
	) . '</p></div>';
	delete_transient( 'fyzsxnb_pubmeta_notice_' . $post_id );
}
add_action( 'admin_notices', 'fyzsxnb_pubmeta_admin_notices' );
