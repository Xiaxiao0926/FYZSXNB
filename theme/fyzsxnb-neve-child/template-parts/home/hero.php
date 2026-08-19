<?php
/**
 * Home hero (EN/RU shared). Sole H1 lives here.
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg   = isset( $args['cfg'] ) ? $args['cfg'] : array();
$hero  = isset( $cfg['hero'] ) ? $cfg['hero'] : array();
$lang  = isset( $cfg['lang'] ) ? $cfg['lang'] : 'en';
$ru    = 'ru' === $lang;
$switch_label = isset( $cfg['lang_switch']['label'] ) ? $cfg['lang_switch']['label'] : ( $ru ? 'EN' : 'RU' );
$switch_url   = isset( $cfg['lang_switch']['url'] ) ? $cfg['lang_switch']['url'] : home_url( '/' );
$switch_hreflang = isset( $cfg['lang_switch']['hreflang'] ) ? $cfg['lang_switch']['hreflang'] : ( $ru ? 'en' : 'ru' );
$nav = isset( $cfg['nav'] ) ? $cfg['nav'] : array();
?>
<header class="fyz-locale-header" aria-label="<?php echo esc_attr( $ru ? 'Навигация FYZSXNB' : 'FYZSXNB English navigation' ); ?>">
  <div class="fyz-inner fyz-locale-header__inner">
    <a class="fyz-locale-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="FYZSXNB home">FYZSXNB</a>
    <nav class="fyz-locale-header__nav" aria-label="<?php echo esc_attr( $ru ? 'Основная навигация' : 'Primary navigation' ); ?>">
      <?php foreach ( $nav as $item ) : ?>
        <?php $url = isset( $item['spec'] ) ? fyzsxnb_home_target( $item['spec'] ) : null; ?>
        <?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $item['label'] ); ?></a><?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <nav class="fyz-locale-switch" aria-label="<?php echo esc_attr( $ru ? 'Язык' : 'Language' ); ?>">
      <strong aria-current="page"><?php echo esc_html( $ru ? 'RU' : 'EN' ); ?></strong><span aria-hidden="true">/</span>
      <a href="<?php echo esc_url( $switch_url ); ?>" hreflang="<?php echo esc_attr( $switch_hreflang ); ?>"><?php echo esc_html( $switch_label ); ?></a>
    </nav>
  </div>
</header>
<main class="fyz-home" lang="<?php echo esc_attr( $lang ); ?>">

<section class="fyz-band fyz-hero" aria-labelledby="fyz-home-title">
  <div class="fyz-inner fyz-hero__grid">
    <div>
      <?php if ( ! empty( $hero['eyebrow'] ) ) : ?><p class="fyz-eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?></p><?php endif; ?>
      <h1 id="fyz-home-title"><?php echo esc_html( isset( $hero['title'] ) ? $hero['title'] : '' ); ?></h1>
      <?php if ( ! empty( $hero['deck'] ) ) : ?><p class="fyz-hero__deck"><?php echo esc_html( $hero['deck'] ); ?></p><?php endif; ?>
      <?php if ( ! empty( $hero['topic_links'] ) ) : ?>
        <ul class="fyz-topic-links" aria-label="<?php echo esc_attr( $ru ? 'Разделы исследований' : 'Research desks' ); ?>">
          <?php foreach ( $hero['topic_links'] as $tl ) : ?>
            <?php $url = fyzsxnb_home_target( $tl['spec'] ); if ( $url ) : ?>
              <li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $tl['label'] ); ?></a></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ( ! empty( $hero['promise'] ) ) : ?>
        <div class="fyz-promise" aria-label="<?php echo esc_attr( $ru ? 'Редакционные принципы' : 'Editorial principles' ); ?>">
          <?php foreach ( $hero['promise'] as $pr ) : ?>
            <div><strong><?php echo esc_html( $pr['strong'] ); ?></strong><span><?php echo esc_html( $pr['span'] ); ?></span></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php if ( ! empty( $hero['hero_story'] ) ) : ?>
      <?php
      $story = $hero['hero_story'];
      $post  = get_page_by_path( isset( $story['spec']['slug'] ) ? $story['spec']['slug'] : '', OBJECT, 'post' );
      $story_url = $post ? get_permalink( $post ) : null;
      $story_img = $post ? get_the_post_thumbnail_url( $post, 'large' ) : '';
      $alt = $post ? get_the_title( $post ) : '';
      ?>
      <?php if ( $story_url ) : ?>
        <a class="fyz-hero-story" href="<?php echo esc_url( $story_url ); ?>" aria-label="<?php echo esc_attr( isset( $story['title'] ) ? $story['title'] : $alt ); ?>">
          <?php if ( $story_img ) : ?><img src="<?php echo esc_url( $story_img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" fetchpriority="high" decoding="async" /><?php endif; ?>
          <div class="fyz-hero-story__body">
            <?php if ( ! empty( $story['meta'] ) ) : ?><span class="fyz-meta"><?php echo esc_html( $story['meta'] ); ?></span><?php endif; ?>
            <h2><?php echo esc_html( isset( $story['title'] ) ? $story['title'] : '' ); ?></h2>
            <?php if ( ! empty( $story['p'] ) ) : ?><p><?php echo esc_html( $story['p'] ); ?></p><?php endif; ?>
          </div>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>