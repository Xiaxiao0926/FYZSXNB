<?php
/**
 * Taxonomy archive template for fyz_vehicle (Cars from China).
 *
 * Renders a brand page (parent term) or a model page (child term) using the
 * Cars from China render helpers. Language filtering (RU vs EN) is applied
 * inside the helpers; empty sections never render.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = get_queried_object();
$body = '';

if ( $term && isset( $term->taxonomy ) && 'fyz_vehicle' === $term->taxonomy ) {
	$body = $term->parent
		? fyzsxnb_cfc_render_model( $term )
		: fyzsxnb_cfc_render_brand( $term );
}
?>
<main id="content" class="fyz-design-system fyz-wire-desk cfc-desk">
	<div class="container cfc-wrap">
		<?php
		if ( $body ) {
			echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal esc_html/esc_url.
		} else {
			echo '<p class="cfc-deck">Cars from China</p>';
		}
		?>
	</div>
</main>
<?php
get_footer();
