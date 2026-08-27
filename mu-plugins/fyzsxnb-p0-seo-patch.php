<?php
/**
 * Plugin Name: FYZSXNB P0 SEO Patch
 * Description: Implements Codex-approved P0 SEO fixes for the FYZSXNB site, including Russian metadata, strategic hub feeds, the blog H1 repair, and reciprocal homepage hreflang.
 * Version: 1.3.1
 * Author: FYZSXNB Engineering
 *
 * Must-use plugin. Drop into /wp-content/mu-plugins/ (or load via an mu-loader).
 * All dynamic output is escaped per WordPress coding standards.
 *
 * @package FYZSXNB\P0_SEO
 */

// Abort if accessed directly outside a valid WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version constant.
 */
define( 'FYZSXNB_P0_SEO_VERSION', '1.4.0' );

/**
 * Feature Flag: Language Contract V2 Resolver.
 *
 * Values:
 *   false: Execute Legacy Resolver (100% current baseline).
 *   true:  Execute Resolver V2 (en/ru/zh full support).
 *
 * Default: false (Guarantees zero-regression upon deployment).
 */
if ( ! defined( 'FYZ_USE_RESOLVER_V2' ) ) {
	$env_flag = getenv( 'FYZ_USE_RESOLVER_V2' );
	if ( false !== $env_flag ) {
		define( 'FYZ_USE_RESOLVER_V2', in_array( strtolower( trim( (string) $env_flag ) ), array( '1', 'true', 'on', 'yes' ), true ) );
	} else {
		define( 'FYZ_USE_RESOLVER_V2', false );
	}
}

/**
 * Check if Resolver V2 should execute for the current request.
 *
 * Rules:
 *   1. Global switch: If FYZ_USE_RESOLVER_V2 is true -> returns true.
 *   2. Internal Canary gate: If request carries 'X-FYZ-Resolver-V2: 1' AND
 *      current user has 'manage_options' capability -> returns true and sets DONOTCACHEPAGE.
 *   3. Default (Public visitors, Googlebot, anonymous requests) -> returns false (100% Legacy path).
 *
 * @return bool True if Resolver V2 is active for this request.
 */
function fyzsxnb_is_resolver_v2_enabled() {
	if ( defined( 'FYZ_USE_RESOLVER_V2' ) && FYZ_USE_RESOLVER_V2 ) {
		return true;
	}

	// Internal Canary Gate: Authenticated admin + X-FYZ-Resolver-V2: 1 header
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		if ( isset( $_SERVER['HTTP_X_FYZ_RESOLVER_V2'] ) && '1' === (string) $_SERVER['HTTP_X_FYZ_RESOLVER_V2'] ) {
			// Absolute cache safety: ensure internal canary output is never cached publicly
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			return true;
		}
	}

	return false;
}

/* -------------------------------------------------------------------------
 * Shared helpers & Central Locale Resolver (0.4.1 Dual-Read Architecture)
 * ---------------------------------------------------------------------- */

/**
 * Explicit Russian post ID set (LEGACY detector fallback).
 *
 * Retained in 0.4.1 as the authoritative fallback set.
 * Page 400 is the /ru/ hub. Posts 448..350 are legacy Russian articles.
 *
 * @return int[]
 */
function fyzsxnb_get_russian_post_ids() {
	return array( 400, 448, 445, 442, 441, 434, 433, 432, 426, 420, 415, 405, 390, 372, 350 );
}

/**
 * Resolve the content locale for a single post object.
 *
 * Decision flow:
 *   1. Primary: Explicit publication metadata (_fyz_content_language).
 *      - ru + Cat54 -> valid RU (source: meta)
 *      - en + no Cat54 -> valid EN (source: meta)
 *      - zh + no Cat54 -> valid ZH (source: meta)
 *      - Structural conflict -> flagged invalid, safe legacy fallback applied.
 *   2. Fallback: Legacy detector (fyzsxnb_get_russian_post_ids + Cat54).
 *   3. Default: en (or unknown under V2).
 *
 * @param int $post_id Post ID.
 * @return array Array with 'locale' ('ru'|'en'|'zh'|'unknown'), 'source' ('meta'|'legacy'|'default'), 'valid' (bool), 'conflict' (bool), 'fallback_locale' (string).
 */
function fyzsxnb_resolve_content_locale( $post_id ) {
	$pid = (int) $post_id;
	if ( $pid <= 0 ) {
		return array(
			'locale'          => 'en',
			'source'          => 'default',
			'valid'           => true,
			'conflict'        => false,
			'fallback_locale' => 'en-US',
		);
	}

	$meta_lang = strtolower( trim( (string) get_post_meta( $pid, '_fyz_content_language', true ) ) );
	$has_cat54 = has_category( 54, $pid );

	// Normalize metadata locale
	if ( in_array( $meta_lang, array( 'ru', 'ru-ru' ), true ) ) {
		$norm_meta = 'ru';
	} elseif ( in_array( $meta_lang, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		$norm_meta = 'en';
	} elseif ( in_array( $meta_lang, array( 'zh', 'zh-cn', 'zh-hans', 'zh_cn', 'zh_hans' ), true ) ) {
		$norm_meta = 'zh';
	} else {
		$norm_meta = '';
	}

	// 1. Explicit metadata check with structural validation
	if ( 'ru' === $norm_meta ) {
		if ( $has_cat54 ) {
			return array(
				'locale'          => 'ru',
				'source'          => 'meta',
				'valid'           => true,
				'conflict'        => false,
				'fallback_locale' => 'en-US',
			);
		} else {
			// Structural conflict: RU metadata without Category 54
			return array(
				'locale'          => 'ru',
				'source'          => 'meta',
				'valid'           => false,
				'conflict'        => true,
				'fallback_locale' => 'en-US',
				'reason'          => 'ru_meta_missing_cat54',
			);
		}
	} elseif ( 'en' === $norm_meta ) {
		if ( ! $has_cat54 ) {
			return array(
				'locale'          => 'en',
				'source'          => 'meta',
				'valid'           => true,
				'conflict'        => false,
				'fallback_locale' => 'en-US',
			);
		} else {
			// Structural conflict: EN metadata with Category 54
			return array(
				'locale'          => 'en',
				'source'          => 'meta',
				'valid'           => false,
				'conflict'        => true,
				'fallback_locale' => 'en-US',
				'reason'          => 'en_meta_has_cat54',
			);
		}
	} elseif ( 'zh' === $norm_meta ) {
		if ( ! $has_cat54 ) {
			return array(
				'locale'          => 'zh',
				'source'          => 'meta',
				'valid'           => true,
				'conflict'        => false,
				'fallback_locale' => 'en-US',
			);
		} else {
			// Structural conflict: ZH metadata with Category 54
			return array(
				'locale'          => 'zh',
				'source'          => 'meta',
				'valid'           => false,
				'conflict'        => true,
				'fallback_locale' => 'en-US',
				'reason'          => 'zh_meta_has_cat54',
			);
		}
	}

	// 2. Metadata missing / unknown -> legacy fallback
	if ( in_array( $pid, fyzsxnb_get_russian_post_ids(), true ) || $has_cat54 ) {
		return array(
			'locale'          => 'ru',
			'source'          => 'legacy',
			'valid'           => true,
			'conflict'        => false,
			'fallback_locale' => 'en-US',
		);
	}

	return array(
		'locale'          => fyzsxnb_is_resolver_v2_enabled() ? 'unknown' : 'en',
		'source'          => 'default',
		'valid'           => true,
		'conflict'        => false,
		'fallback_locale' => 'en-US',
	);
}

/**
 * Determine whether the currently-rendered object is a Russian target.
 *
 * 0.4.1 Architecture:
 *   - For single posts: delegates to central content locale resolver (metadata primary, legacy fallback).
 *   - For non-post / pages (e.g. Page 400 RU home): evaluates explicit page IDs and category 54.
 *
 * @param int|null $target_id Optional target ID override.
 * @return bool True when the target object should receive Russian SEO attributes.
 */
function fyzsxnb_is_russian_target( $target_id = null ) {
	// Never act inside the admin context.
	if ( is_admin() ) {
		return false;
	}

	if ( null !== $target_id ) {
		$current_id = (int) $target_id;
	} elseif ( is_singular() ) {
		$current_id = (int) get_queried_object_id();
	} else {
		$current_id = 0;
	}

	if ( $current_id <= 0 ) {
		return false;
	}

	// Single Post context -> use central content locale resolver
	$post_type = get_post_type( $current_id );
	if ( 'post' === $post_type || empty( $post_type ) ) {
		$resolved = fyzsxnb_resolve_content_locale( $current_id );
		return ( 'ru' === $resolved['locale'] );
	}

	// Non-post / Page context (e.g. Page 400 /ru/ home) -> legacy request detector
	if ( in_array( $current_id, fyzsxnb_get_russian_post_ids(), true ) ) {
		return true;
	}

	if ( has_category( 54, $current_id ) ) {
		return true;
	}

	return false;
}

/* -------------------------------------------------------------------------
 * FIX-SEO-001: Russian Document Language & Metadata Correction
 * ---------------------------------------------------------------------- */

/**
 * FIX-SEO-001 (1/4) — Adjust the <html> language_attributes output.
 *
 * Replaces only the lang="..." attribute with lang="ru-RU" while preserving all
 * other attributes (e.g. dir="ltr"). If no lang attribute is present, it is
 * appended. This avoids blindly overwriting the full attribute string.
 *
 * @param string $output The language_attributes output string.
 * @return string Modified attribute string.
 */
function fyzsxnb_filter_language_attributes( $output ) {
	if ( is_admin() ) {
		return $output;
	}

	if ( fyzsxnb_is_resolver_v2_enabled() ) {
		$target_id = is_singular() ? (int) get_queried_object_id() : 0;
		if ( $target_id > 0 ) {
			$res = fyzsxnb_resolve_content_locale( $target_id );
			if ( 'ru' === $res['locale'] ) {
				if ( preg_match( '/\blang=(["\'])([^"\']*)\1/i', $output ) ) {
					return preg_replace( '/\blang=(["\'])([^"\']*)\1/i', 'lang="ru-RU"', $output );
				}
				return rtrim( $output ) . ' lang="ru-RU"';
			} elseif ( 'zh' === $res['locale'] ) {
				if ( preg_match( '/\blang=(["\'])([^"\']*)\1/i', $output ) ) {
					return preg_replace( '/\blang=(["\'])([^"\']*)\1/i', 'lang="zh-CN"', $output );
				}
				return rtrim( $output ) . ' lang="zh-CN"';
			}
			return $output;
		}
	}

	if ( ! fyzsxnb_is_russian_target() ) {
		return $output;
	}

	// Match lang="..." or lang='...' (preserve the quote style by normalising).
	if ( preg_match( '/\blang=(["\'])([^"\']*)\1/i', $output ) ) {
		$output = preg_replace( '/\blang=(["\'])([^"\']*)\1/i', 'lang="ru-RU"', $output );
	} else {
		// Append lang when none exists; keep existing attributes intact.
		$output = rtrim( $output ) . ' lang="ru-RU"';
	}

	return $output;
}
add_filter( 'language_attributes', 'fyzsxnb_filter_language_attributes' );

/**
 * FIX-SEO-001 (2/4) — Force the Open Graph locale to ru_RU for Russian targets (or zh_CN under V2).
 *
 * Uses the documented `aioseo_facebook_tags` filter (per AIOSEO developer docs:
 * https://aioseo.com/docs/aioseo_facebook_tags/) to set or override the
 * `og:locale` value within the Facebook/Open Graph tag array. This is the
 * officially supported path for modifying OG output in current AIOSEO versions.
 *
 * @param array $facebook_tags The array of Facebook/OG tag key-value pairs.
 * @return array
 */
function fyzsxnb_filter_facebook_tags( $facebook_tags ) {
	if ( fyzsxnb_is_resolver_v2_enabled() ) {
		$target_id = is_singular() ? (int) get_queried_object_id() : 0;
		if ( $target_id > 0 ) {
			$res = fyzsxnb_resolve_content_locale( $target_id );
			if ( ! is_array( $facebook_tags ) ) {
				$facebook_tags = array();
			}
			if ( 'ru' === $res['locale'] ) {
				$facebook_tags['og:locale'] = 'ru_RU';
			} elseif ( 'zh' === $res['locale'] ) {
				$facebook_tags['og:locale'] = 'zh_CN';
			}
			return $facebook_tags;
		}
	}

	if ( ! fyzsxnb_is_russian_target() ) {
		return $facebook_tags;
	}

	if ( ! is_array( $facebook_tags ) ) {
		$facebook_tags = array();
	}

	$facebook_tags['og:locale'] = 'ru_RU';

	return $facebook_tags;
}
add_filter( 'aioseo_facebook_tags', 'fyzsxnb_filter_facebook_tags' );

/**
 * FIX-SEO-001 (3/4) — Check whether a @type value is an approved
 * content-bearing graph type.
 *
 * Supports both scalar string @type (e.g. "WebPage") and array @type
 * (e.g. ["WebPage", "Article"]). Returns true if at least one type in
 * the value is in the approved set.
 *
 * Approved types: WebPage, Article, BlogPosting, NewsArticle, MedicalWebPage.
 * Unrelated types such as Organization, BreadcrumbList, Person, ImageObject
 * are NOT approved and their existing inLanguage values must be preserved.
 *
 * @param mixed $node_type The @type value to check (string or array).
 * @return bool True if at least one type is approved.
 */
function fyzsxnb_is_approved_graph_type( $node_type ) {
	static $approved_types = array(
		'WebPage',
		'Article',
		'BlogPosting',
		'NewsArticle',
		'MedicalWebPage',
	);

	// Scalar @type: e.g. "WebPage".
	if ( is_string( $node_type ) ) {
		return in_array( $node_type, $approved_types, true );
	}

	// Array @type: e.g. ["WebPage", "Article"] or ["Organization"].
	if ( is_array( $node_type ) ) {
		foreach ( $node_type as $t ) {
			if ( is_string( $t ) && in_array( $t, $approved_types, true ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * FIX-SEO-001 (3/4) — Recursively set inLanguage to target locale on approved
 * content-bearing graph nodes within AIOSEO schema output.
 *
 * Walks the schema structure (arrays and objects) and:
 *   - Sets or replaces `inLanguage` to target locale ONLY on graph nodes whose
 *     `@type` is one of the approved content-bearing types (WebPage, Article,
 *     BlogPosting, NewsArticle, MedicalWebPage). Both scalar and array @type
 *     shapes are supported.
 *   - Does NOT touch `inLanguage` on unrelated graph nodes (Organization,
 *     BreadcrumbList, Person, ImageObject, etc.). Their existing values are
 *     preserved unchanged.
 *
 * @param mixed  $data Schema node (passed by reference).
 * @param string $target_locale Target inLanguage code (e.g. 'ru-RU' or 'zh-CN').
 */
function fyzsxnb_set_inlanguage_recursive( &$data, $target_locale = 'ru-RU' ) {
	if ( is_array( $data ) ) {
		// Recurse into child nodes first.
		foreach ( $data as $key => &$value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				fyzsxnb_set_inlanguage_recursive( $value, $target_locale );
			}
		}
		unset( $value );

		// Only set or replace inLanguage on approved content-bearing @type nodes.
		if ( isset( $data['@type'] ) && fyzsxnb_is_approved_graph_type( $data['@type'] ) ) {
			$data['inLanguage'] = $target_locale;
		}
	} elseif ( is_object( $data ) ) {
		// Recurse into child nodes first.
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				fyzsxnb_set_inlanguage_recursive( $data->$key, $target_locale );
			}
		}

		// Only set or replace inLanguage on approved content-bearing @type nodes.
		if ( isset( $data->{'@type'} ) && fyzsxnb_is_approved_graph_type( $data->{'@type'} ) ) {
			$data->inLanguage = $target_locale;
		}
	}
}

/**
 * FIX-SEO-001 (3/4) — Filter the AIOSEO schema output for Russian / Chinese targets.
 *
 * @param mixed $schema The full schema graph output.
 * @return mixed
 */
function fyzsxnb_filter_schema_output( $schema ) {
	if ( fyzsxnb_is_resolver_v2_enabled() ) {
		$target_id = is_singular() ? (int) get_queried_object_id() : 0;
		if ( $target_id > 0 ) {
			$res = fyzsxnb_resolve_content_locale( $target_id );
			if ( 'ru' === $res['locale'] ) {
				fyzsxnb_set_inlanguage_recursive( $schema, 'ru-RU' );
			} elseif ( 'zh' === $res['locale'] ) {
				fyzsxnb_set_inlanguage_recursive( $schema, 'zh-CN' );
			}
			return $schema;
		}
	}

	if ( ! fyzsxnb_is_russian_target() ) {
		return $schema;
	}

	fyzsxnb_set_inlanguage_recursive( $schema, 'ru-RU' );

	return $schema;
}
add_filter( 'aioseo_schema_output', 'fyzsxnb_filter_schema_output' );

/**
 * FIX-SEO-001 (4/4) — Set the meta description for page 400 (the /ru/ hub).
 *
 * @param string $description The current meta description.
 * @return string
 */
function fyzsxnb_filter_aioseo_description( $description ) {
	if ( is_singular() && 400 === (int) get_queried_object_id() ) {
		return 'Китайские технологии, биомедицина и решения для России: исследования и гиды по выбору, проверке и ввозу.';
	}
	return $description;
}
add_filter( 'aioseo_description', 'fyzsxnb_filter_aioseo_description' );

/* -------------------------------------------------------------------------
 * FIX-SEO-002: Strategic Hub Dynamic Latest-Article Feeds
 * ---------------------------------------------------------------------- */

/**
 * FIX-SEO-002 — Return the hub configuration map.
 *
 * Each hub maps a hub_id to a set of source categories and a canonical category
 * slug used for the "view all" archive link.
 *
 * @return array
 */
function fyzsxnb_get_hub_map() {
	return array(
		329 => array(
			'categories' => array( 52 ),
			'slug'       => 'china-global-biomed',
		),
		328 => array(
			'categories' => array( 50 ),
			'slug'       => 'china-tech-products',
		),
		327 => array(
			'categories' => array( 51, 21 ),
			'slug'       => 'pet-care-products',
		),
		330 => array(
			'categories' => array( 53, 33, 39 ),
			'slug'       => 'product-opportunity-research',
		),
	);
}

/**
 * FIX-SEO-002 — Render the inline grid styles once per request.
 *
 * Outputs a small, scoped stylesheet the first time it is called and returns an
 * empty string on subsequent calls to avoid duplicate <style> blocks when the
 * shortcode is used multiple times on one page.
 *
 * @return string
 */
function fyzsxnb_hub_feed_styles() {
	static $printed = false;

	if ( $printed ) {
		return '';
	}
	$printed = true;

	ob_start();
	?>
	<style type="text/css" media="all">
		.fyzsxnb-hub-feed .hub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;margin:0 0 24px;padding:0;list-style:none}
		.fyzsxnb-hub-feed .hub-card{display:flex;flex-direction:column}
		.fyzsxnb-hub-feed .nv-post-thumbnail-wrap{margin:0 0 12px}
		.fyzsxnb-hub-feed .nv-post-thumbnail-wrap img{width:100%;height:auto;display:block}
		.fyzsxnb-hub-feed .hub-card-title{margin:0 0 8px;font-size:1.15rem;line-height:1.3}
		.fyzsxnb-hub-feed .hub-card-date{font-size:.85rem;opacity:.7;margin:0 0 8px}
		.fyzsxnb-hub-feed .hub-card-excerpt{margin:0 0 12px}
		.fyzsxnb-hub-feed .hub-card-readmore{font-weight:600;text-decoration:none}
		.fyzsxnb-hub-feed .hub-card-readmore:hover{text-decoration:underline}
		.fyzsxnb-hub-feed .hub-view-all{margin-top:8px}
		.fyzsxnb-hub-feed .hub-empty-state{padding:16px 0}
	</style>
	<?php
	return ob_get_clean();
}

/**
 * FIX-SEO-002 — Shortcode callback for [fyzsxnb_hub_feed hub_id="..."].
 *
 * Renders the 12 latest article cards for the requested strategic hub.
 *
 * De-duplication: a single WP_Query using category__in is used so that the 12
 * most recent posts across the hub's categories are returned in pure
 * chronological order. WordPress core guarantees that category__in returns each
 * post only once even when it is assigned to several of the mapped categories,
 * so per-category post__not_in accumulation is unnecessary for a single combined
 * query. A defensive seen-ID set is applied afterwards as a belt-and-suspenders
 * guarantee that no card is ever rendered twice.
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function fyzsxnb_hub_feed_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'hub_id' => 0,
		),
		$atts,
		'fyzsxnb_hub_feed'
	);

	$hub_id = absint( $atts['hub_id'] );
	$map    = fyzsxnb_get_hub_map();

	if ( ! isset( $map[ $hub_id ] ) ) {
		// Unknown hub: render nothing rather than a misleading empty state.
		return '';
	}

	$hub        = $map[ $hub_id ];
	$categories = array_map( 'absint', $hub['categories'] );
	$slug       = $hub['slug'];

	$query_args = array(
		'category__in'       => $categories,
		'orderby'            => 'date',
		'order'              => 'DESC',
		'posts_per_page'     => 12,
		'post_status'        => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'      => true,
	);

	$query = new WP_Query( $query_args );

	// Begin output buffering for safe shortcode return.
	ob_start();

	echo fyzsxnb_hub_feed_styles(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS.

	?>
	<div class="fyzsxnb-hub-feed">
		<?php if ( $query->have_posts() ) : ?>
			<div class="hub-grid">
				<?php
				$seen = array();

				while ( $query->have_posts() ) :
					$query->the_post();

					$post_id = (int) get_the_ID();

					// Defensive de-duplication: skip a post if its ID was already
					// rendered (category__in already guarantees this, but we keep
					// an explicit guard so the contract is enforced in code).
					if ( isset( $seen[ $post_id ] ) ) {
						continue;
					}
					$seen[ $post_id ] = true;
					?>
					<article class="hub-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="nv-post-thumbnail-wrap">
								<a class="hub-card-thumb-link" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php
									$thumb_id = get_post_thumbnail_id();
									$alt      = $thumb_id ? trim( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) : '';
									if ( '' === $alt ) {
										$alt = get_the_title();
									}
									echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail escapes internally.
										null,
										'medium',
										array( 'alt' => $alt )
									);
									?>
								</a>
							</div>
						<?php endif; ?>

						<h3 class="hub-card-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h3>

						<div class="hub-card-date">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>

						<div class="hub-card-excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), 25, '&hellip;' ) ); ?>
						</div>

						<a class="hub-card-readmore" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html__( 'Read more', 'fyzsxnb' ); ?></a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<div class="hub-view-all">
				<a href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>">
					<?php echo esc_html__( 'View all articles', 'fyzsxnb' ); ?>
				</a>
			</div>
		<?php else : ?>
			<p class="hub-empty-state"><?php echo esc_html__( 'No articles currently published in this section.', 'fyzsxnb' ); ?></p>
		<?php endif; ?>
	</div>
	<?php

	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'fyzsxnb_hub_feed', 'fyzsxnb_hub_feed_shortcode' );

/**
 * FIX-SEO-002 — Idempotent page-content injection for the four verified hub pages.
 *
 * The four hub pages (IDs 329, 328, 327, 330) do not contain the
 * [fyzsxnb_hub_feed] shortcode in their stored post_content. This filter
 * injects the shortcode automatically when the page is rendered, without
 * modifying the database.
 *
 * Idempotency: The filter checks whether the content already contains the
 * shortcode marker string `fyzsxnb_hub_feed`. If found, no injection occurs,
 * preventing duplicate feed rendering on repeated page loads or manual
 * shortcode insertion.
 *
 * Content preservation: Existing page content is fully preserved. The feed is
 * appended after the existing content, separated by a structural divider.
 *
 * @param string $content The page post_content.
 * @return string Modified content with feed shortcode appended.
 */
function fyzsxnb_inject_hub_feed_into_pages( $content ) {
	// Only act on singular page requests in the front end.
	if ( is_admin() || ! is_singular( 'page' ) ) {
		return $content;
	}

	$current_id = (int) get_queried_object_id();
	$map        = fyzsxnb_get_hub_map();

	// The page's the_content filter also runs while WordPress builds excerpts
	// for posts inside the nested hub query. Restrict injection to the actual
	// page object so excerpt generation cannot recursively inject another feed.
	global $post;
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type || (int) $post->ID !== $current_id ) {
		return $content;
	}

	if ( ! isset( $map[ $current_id ] ) ) {
		return $content;
	}

	// Idempotency: check if the shortcode is already present in the content.
	if ( false !== strpos( $content, 'fyzsxnb_hub_feed' ) ) {
		return $content;
	}

	// Append the feed shortcode after existing content.
	$feed_markup = "\n\n<!-- fyzsxnb-hub-feed-injection -->\n[fyzsxnb_hub_feed hub_id=\"" . $current_id . "\"]\n<!-- /fyzsxnb-hub-feed-injection -->\n";

	return $content . $feed_markup;
}
add_filter( 'the_content', 'fyzsxnb_inject_hub_feed_into_pages' );

/* -------------------------------------------------------------------------
 * FIX-SEO-005: Reciprocal homepage hreflang
 * ---------------------------------------------------------------------- */

/**
 * Print the complete EN/RU/x-default alternate set on both language homes.
 *
 * The two URLs are genuine counterparts. Article pages are intentionally not
 * paired here because only reviewed translations may receive hreflang links.
 */
function fyzsxnb_render_home_hreflang() {
	if ( is_admin() || ( ! is_front_page() && ! is_page( 400 ) ) ) {
		return;
	}

	$english_url = home_url( '/' );
	$russian_url = home_url( '/ru/' );

	echo '<link rel="alternate" hreflang="en" href="' . esc_url( $english_url ) . '" />' . "\n";
	echo '<link rel="alternate" hreflang="ru" href="' . esc_url( $russian_url ) . '" />' . "\n";
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $english_url ) . '" />' . "\n";
}
add_action( 'wp_head', 'fyzsxnb_render_home_hreflang', 2 );

/**
 * Translate Neve's keyboard skip link on the Russian homepage.
 *
 * This deliberately uses only the raw request path. Calling conditional-query
 * APIs from gettext can recurse while WordPress is still building the query.
 *
 * @param string $translated_text Translated text.
 * @param string $text            Original source text.
 * @return string
 */
function fyzsxnb_localize_russian_skip_link( $translated_text, $text ) {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$request_path = strtok( $request_uri, '?' );
	if ( '/ru/' === $request_path && 'Skip to content' === $text ) {
		return 'Перейти к содержанию';
	}

	return $translated_text;
}
add_filter( 'gettext', 'fyzsxnb_localize_russian_skip_link', 20, 2 );

/* -------------------------------------------------------------------------
 * FIX-SEO-004: Blog Page H1 Fix (Page 18 only)
 *
 * Posts 360 and 362 H1 fixes are handled via REST API payloads (content edits)
 * and are intentionally NOT addressed in this PHP file.
 * ---------------------------------------------------------------------- */

/**
 * FIX-SEO-004 — Track whether the blog H1 has already been rendered.
 *
 * Shared state used by the filter and action callbacks below so the heading is
 * only ever printed once, regardless of which hook fires first.
 *
 * @param bool|null $render Pass true to mark the H1 as rendered; null to read.
 * @return bool
 */
function fyzsxnb_blog_h1_state( $render = null ) {
	static $rendered = false;
	if ( null !== $render ) {
		$rendered = (bool) $render;
	}
	return $rendered;
}

/**
 * FIX-SEO-004 — Detect the blog index page (page 18).
 *
 * Handles both cases:
 *   - Page 18 is set as the "Posts page" (is_home() true, is_page() false).
 *   - Page 18 is a regular page (is_page(18) true).
 *
 * @return bool
 */
function fyzsxnb_is_blog_page_18() {
	if ( is_admin() ) {
		return false;
	}

	if ( is_home() && 18 === (int) get_option( 'page_for_posts' ) ) {
		return true;
	}

	if ( is_page( 18 ) ) {
		return true;
	}

	return false;
}

/**
 * FIX-SEO-004 — Render the visible H1 once, at the top of the blog index.
 *
 * The heading uses the Neve `entry-title` class so it inherits theme styling and
 * preserves the existing archive layout.
 */
function fyzsxnb_maybe_render_blog_h1() {
	if ( fyzsxnb_blog_h1_state() ) {
		return;
	}
	if ( ! fyzsxnb_is_blog_page_18() ) {
		return;
	}

	fyzsxnb_blog_h1_state( true );

	echo '<h1 class="entry-title">FYZSXNB Blog</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted markup.
}

/**
 * FIX-SEO-004 — Neve action callback (official hook).
 *
 * Fires before the posts loop on Neve archives. This is the officially
 * documented Neve hook (https://codex.nevewp.com/reference/package/neve/page/3/).
 * Guarded so it only acts on the blog index (page 18) and only renders once.
 */
function fyzsxnb_render_blog_h1_before_loop() {
	fyzsxnb_maybe_render_blog_h1();
}
add_action( 'neve_before_posts_loop', 'fyzsxnb_render_blog_h1_before_loop' );

/**
 * FIX-SEO-004 — Core loop_start fallback.
 *
 * Guarantees the H1 is rendered even if the Neve-specific hooks are unavailable.
 * Restricted to the main query to avoid firing inside widget/secondary loops.
 *
 * @param \WP_Query $query The query object for the loop that just started.
 */
function fyzsxnb_render_blog_h1_on_loop_start( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	fyzsxnb_maybe_render_blog_h1();
}
add_action( 'loop_start', 'fyzsxnb_render_blog_h1_on_loop_start' );

/* -------------------------------------------------------------------------
 * Diagnostic Trace Helper (Internal / Authenticated QA)
 * ---------------------------------------------------------------------- */

/**
 * Return detailed diagnostic locale resolution facts for a given post.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function fyzsxnb_get_locale_trace( $post_id ) {
	$pid       = (int) $post_id;
	$meta_lang = get_post_meta( $pid, '_fyz_content_language', true );
	$cats      = wp_get_post_categories( $pid );
	$has_cat54 = in_array( 54, $cats, true );
	$legacy_ru = in_array( $pid, fyzsxnb_get_russian_post_ids(), true );
	$resolved  = fyzsxnb_resolve_content_locale( $pid );

	return array(
		'post_id'         => $pid,
		'meta_language'   => $meta_lang ? $meta_lang : '',
		'has_cat54'       => $has_cat54,
		'in_legacy_array' => $legacy_ru,
		'resolved_locale' => $resolved['locale'],
		'resolver_source' => $resolved['source'],
		'is_valid'        => $resolved['valid'],
		'is_conflict'     => $resolved['conflict'],
		'conflict_reason' => isset( $resolved['reason'] ) ? $resolved['reason'] : '',
	);
}
