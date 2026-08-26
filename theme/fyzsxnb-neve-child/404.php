<?php
/**
 * Human-readable 404 page (EN/RU localized).
 *
 * @package FYZSXNB_Neve_Child
 */

get_header();
$ru = fyzsxnb_is_russian_view();
$title = $ru ? 'Страница не найдена' : 'This page could not be found';
$copy  = $ru ? 'Возможно, адрес изменился. Воспользуйтесь поиском или перейдите к аналитическим разделам и техническим отчётам.' : 'The requested page may have moved. Search our knowledge base or navigate to our industry intelligence sections.';
$links_label = $ru ? 'Полезные ссылки' : 'Useful links';
$link_home = $ru ? 'Английская главная' : 'English homepage';
$link_ru   = $ru ? 'Русские решения' : 'Russian solutions';
$link_biomed = $ru ? 'Архив Biomed' : 'Biomed archive';
?>
<main id="content" class="neve-main">
	<section class="fyz-error-shell" aria-labelledby="fyz-error-title">
		<p class="fyz-error-code">404</p>
		<h1 id="fyz-error-title" class="fyz-error-title"><?php echo esc_html( $title ); ?></h1>
		<p class="fyz-error-copy"><?php echo esc_html( $copy ); ?></p>
		<?php get_search_form(); ?>
		<nav class="fyz-error-links" aria-label="<?php echo esc_attr( $links_label ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $link_home ); ?></a>
			<a href="<?php echo esc_url( home_url( '/ru/' ) ); ?>"><?php echo esc_html( $link_ru ); ?></a>
			<a href="<?php echo esc_url( home_url( '/category/china-global-biomed/' ) ); ?>"><?php echo esc_html( $link_biomed ); ?></a>
		</nav>
	</section>
</main>
<?php
get_footer();
