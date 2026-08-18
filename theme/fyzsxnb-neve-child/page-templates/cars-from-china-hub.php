<?php
/**
 * Template Name: Cars from China Hub
 *
 * Renders the Cars from China desk hub (EN or RU, detected from the request
 * path). Content sections are language-aware; empty sections are suppressed.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="fyz-design-system fyz-wire-desk cfc-desk">
	<div class="container cfc-wrap">
		<?php echo fyzsxnb_cfc_render_hub(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal esc_html/esc_url. ?>
	</div>
</main>
<?php
get_footer();
