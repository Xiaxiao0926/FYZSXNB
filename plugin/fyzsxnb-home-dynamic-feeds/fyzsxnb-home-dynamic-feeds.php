<?php
/**
 * Plugin Name: FYZSXNB Home Dynamic Feeds
 * Description: Language-pure Latest signals and Latest guides feeds with homepage cache invalidation.
 * Version: 1.0.0
 * Author: FYZSXNB Engineering
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fyzsxnb_home_post_locale( $post_id ) {
	$declared = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_language', true ) ) );
	if ( in_array( $declared, array( 'ru', 'ru-ru' ), true ) ) {
		return 'ru-RU';
	}
	if ( in_array( $declared, array( 'en', 'en-us', 'en-gb' ), true ) ) {
		return 'en-US';
	}
	if ( has_category( 54, $post_id ) ) {
		return 'ru-RU';
	}
	$title = wp_strip_all_tags( get_the_title( $post_id ) );
	if ( preg_match( '/[\x{0400}-\x{04FF}]/u', $title ) ) {
		return 'ru-RU';
	}
	if ( preg_match( '/[\x{3400}-\x{9FFF}]/u', $title ) ) {
		return '';
	}
	return preg_match( '/[A-Za-z]/', $title ) ? 'en-US' : '';
}

function fyzsxnb_home_is_guide( $post_id, $locale ) {
	$kind = strtolower( trim( (string) get_post_meta( $post_id, '_fyz_content_kind', true ) ) );
	if ( 'guide' === $kind ) {
		return true;
	}
	$haystack = strtolower( get_post_field( 'post_name', $post_id ) . ' ' . wp_strip_all_tags( get_the_title( $post_id ) ) );
	if ( 'ru-RU' === $locale ) {
		return (bool) preg_match( '/(guide|check|repair|verification|гайд|руковод|провер|ремонт|совместим|выбор|ввоз|утильсбор)/u', $haystack );
	}
	return (bool) preg_match( '/(guide|checklist|readiness|procurement|verification|decision-map|how-to|compared)/', $haystack );
}

function fyzsxnb_get_home_feed_posts( $locale, $type, $limit, $exclude = array() ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 80,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
		)
	);
	$posts   = array();
	$exclude = array_map( 'absint', $exclude );
	foreach ( $query->posts as $candidate ) {
		if ( in_array( (int) $candidate->ID, $exclude, true ) || $locale !== fyzsxnb_home_post_locale( $candidate->ID ) ) {
			continue;
		}
		if ( 'guides' === $type && ! fyzsxnb_home_is_guide( $candidate->ID, $locale ) ) {
			continue;
		}
		$posts[] = $candidate;
		if ( count( $posts ) >= $limit ) {
			break;
		}
	}
	return $posts;
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
			$minimum = 'signals' === $type ? 4 : 3;
			$exclude = array();
			if ( 'guides' === $type ) {
				$exclude = wp_list_pluck( fyzsxnb_get_home_feed_posts( $locale, 'signals', 4 ), 'ID' );
			}
			$posts = fyzsxnb_get_home_feed_posts( $locale, $type, $limit, $exclude );
			return count( $posts ) < $minimum ? $matches[0] : fyzsxnb_render_home_feed( $posts, $locale, $type );
		},
		$content
	);
}
add_filter( 'the_content', 'fyzsxnb_replace_home_feed_markers', 20 );

function fyzsxnb_purge_homepage_feeds( $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	clean_post_cache( 11 );
	clean_post_cache( 400 );
	wp_cache_delete( 11, 'posts' );
	wp_cache_delete( 400, 'posts' );
	foreach ( array( home_url( '/' ), home_url( '/ru/' ) ) as $url ) {
		do_action( 'litespeed_purge_url', $url );
		if ( function_exists( 'rocket_clean_files' ) ) {
			rocket_clean_files( array( $url ) );
		}
		if ( function_exists( 'w3tc_flush_url' ) ) {
			w3tc_flush_url( $url );
		}
	}
	do_action( 'fyzsxnb_homepage_feed_cache_purged', $post_id );
}
add_action( 'save_post_post', 'fyzsxnb_purge_homepage_feeds', 20, 1 );
add_action( 'trashed_post', 'fyzsxnb_purge_homepage_feeds', 20, 1 );
add_action( 'untrashed_post', 'fyzsxnb_purge_homepage_feeds', 20, 1 );
add_action( 'before_delete_post', 'fyzsxnb_purge_homepage_feeds', 20, 1 );
