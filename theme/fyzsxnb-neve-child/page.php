<?php
/**
 * Page template — special-cases the RU homepage (page 'ru', id 400) to the
 * shared homepage structure; all other pages keep the Neve parent rendering.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( is_page( 'ru' ) ) {
	fyzsxnb_render_homepage( 'ru-RU' );
} else {
	$neve_page = get_template_directory() . '/page.php';
	if ( file_exists( $neve_page ) ) {
		load_template( $neve_page );
	} else {
		load_template( get_template_directory() . '/index.php' );
	}
}