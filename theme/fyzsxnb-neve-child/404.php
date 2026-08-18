<?php
/**
 * Human-readable 404 page.
 *
 * @package FYZSXNB_Neve_Child
 */

get_header();
?>
<main id="content" class="neve-main">
	<section class="fyz-error-shell" aria-labelledby="fyz-error-title">
		<p class="fyz-error-code">404</p>
		<h1 id="fyz-error-title" class="fyz-error-title"><?php esc_html_e( 'This page could not be found', 'fyzsxnb-neve-child' ); ?></h1>
		<p class="fyz-error-copy"><?php esc_html_e( 'The address may have changed. Search the archive or return to one of the main research desks.', 'fyzsxnb-neve-child' ); ?></p>
		<?php get_search_form(); ?>
		<nav class="fyz-error-links" aria-label="<?php esc_attr_e( 'Useful links', 'fyzsxnb-neve-child' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'English homepage', 'fyzsxnb-neve-child' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/ru/' ) ); ?>"><?php esc_html_e( 'Russian solutions', 'fyzsxnb-neve-child' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/category/china-global-biomed/' ) ); ?>"><?php esc_html_e( 'Biomed archive', 'fyzsxnb-neve-child' ); ?></a>
		</nav>
	</section>
</main>
<?php
get_footer();
