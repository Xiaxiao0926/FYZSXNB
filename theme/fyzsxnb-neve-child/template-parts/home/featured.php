<?php
/**
 * Featured reports (manual editorial selection -> resolver URLs).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$feat = isset( $cfg['featured'] ) ? $cfg['featured'] : array();
$cards = isset( $feat['cards'] ) ? $feat['cards'] : array();
$ru = 'ru-RU' === ( isset( $args['locale'] ) ? $args['locale'] : '' );
$resolved = array();
foreach ( $cards as $card ) {
	$post = get_page_by_path( isset( $card['slug'] ) ? $card['slug'] : '', OBJECT, 'post' );
	if ( ! $post || 'publish' !== $post->post_status ) {
		continue;
	}
	$resolved[] = array(
		'kind'  => isset( $card['kind'] ) ? $card['kind'] : 'compact',
		'label' => isset( $card['label'] ) ? $card['label'] : '',
		'p'     => isset( $card['p'] ) ? $card['p'] : '',
		'cta'   => isset( $card['cta'] ) ? $card['cta'] : '',
		'url'   => get_permalink( $post ),
		'img'   => get_the_post_thumbnail_url( $post, 'large' ),
		'alt'   => get_the_title( $post ),
		'title' => get_the_title( $post ),
	);
}
if ( empty( $resolved ) ) {
	return;
}
?>
<section class="fyz-band fyz-featured" <?php echo ! empty( $feat['section_id'] ) ? 'id="' . esc_attr( $feat['section_id'] ) . '"' : ''; ?> aria-labelledby="fyz-featured-title">
  <div class="fyz-inner">
    <div class="fyz-section-head">
      <div>
        <?php if ( ! empty( $feat['eyebrow'] ) ) : ?><p class="fyz-eyebrow"><?php echo esc_html( $feat['eyebrow'] ); ?></p><?php endif; ?>
        <h2 id="fyz-featured-title"><?php echo esc_html( isset( $feat['title'] ) ? $feat['title'] : '' ); ?></h2>
      </div>
      <?php if ( ! empty( $feat['note'] ) ) : ?><p><?php echo esc_html( $feat['note'] ); ?></p><?php endif; ?>
    </div>
    <div class="fyz-feature-grid">
      <?php $lead = null; $smalls = array(); $compacts = array(); foreach ( $resolved as $c ) { 'lead' === $c['kind'] ? $lead = $c : ( 'small' === $c['kind'] ? $smalls[] = $c : $compacts[] = $c ); } ?>
      <?php if ( $lead ) : ?>
        <article class="fyz-feature-lead">
          <?php if ( $lead['img'] ) : ?><a class="fyz-feature-lead__media" href="<?php echo esc_url( $lead['url'] ); ?>"><img src="<?php echo esc_url( $lead['img'] ); ?>" alt="<?php echo esc_attr( $lead['alt'] ); ?>" loading="eager" decoding="async" /></a><?php endif; ?>
          <div class="fyz-feature-lead__body">
            <?php if ( $lead['label'] ) : ?><p class="fyz-label"><?php echo esc_html( $lead['label'] ); ?></p><?php endif; ?>
            <h3><a href="<?php echo esc_url( $lead['url'] ); ?>"><?php echo esc_html( $lead['title'] ); ?></a></h3>
            <p><?php echo esc_html( $lead['p'] ); ?></p>
            <a class="fyz-arrow" href="<?php echo esc_url( $lead['url'] ); ?>"><?php echo esc_html( $lead['cta'] ); ?> <span aria-hidden="true">→</span></a>
          </div>
        </article>
      <?php endif; ?>
      <?php if ( $smalls ) : ?>
        <div class="fyz-feature-stack">
          <?php foreach ( $smalls as $c ) : ?>
            <article class="fyz-feature-small">
              <?php if ( $c['img'] ) : ?><a class="fyz-feature-small__media" href="<?php echo esc_url( $c['url'] ); ?>"><img src="<?php echo esc_url( $c['img'] ); ?>" alt="<?php echo esc_attr( $c['alt'] ); ?>" loading="eager" decoding="async" /></a><?php endif; ?>
              <div>
                <?php if ( $c['label'] ) : ?><p class="fyz-label"><?php echo esc_html( $c['label'] ); ?></p><?php endif; ?>
                <h3><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['title'] ); ?></a></h3>
                <p><?php echo esc_html( $c['p'] ); ?></p>
                <a class="fyz-arrow" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['cta'] ); ?> <span aria-hidden="true">→</span></a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php if ( $compacts ) : ?>
      <div class="fyz-compact-grid">
        <?php foreach ( $compacts as $c ) : ?>
          <article class="fyz-compact">
            <?php if ( $c['label'] ) : ?><span class="fyz-meta"><?php echo esc_html( $c['label'] ); ?></span><?php endif; ?>
            <h3><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['title'] ); ?></a></h3>
            <p><?php echo esc_html( $c['p'] ); ?></p>
            <a class="fyz-arrow" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['cta'] ); ?> <span aria-hidden="true">→</span></a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>