<?php
/**
 * Desks section (destinations via resolvers; cards may be link-less).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$desks = isset( $cfg['desks'] ) ? $cfg['desks'] : array();
if ( empty( $desks ) ) {
	return;
}
$ru = 'ru-RU' === ( isset( $args['locale'] ) ? $args['locale'] : '' );
?>
<section class="fyz-band fyz-desks" id="topics" aria-labelledby="fyz-desks-title">
  <div class="fyz-inner">
    <div class="fyz-desks__head">
      <div>
        <p class="fyz-eyebrow"><?php echo esc_html( $ru ? 'Разделы исследований' : 'Research desks' ); ?></p>
        <h2 id="fyz-desks-title"><?php echo esc_html( $ru ? 'Что мы исследуем' : 'Follow the question, not the category label' ); ?></h2>
      </div>
      <p><?php echo esc_html( $ru ? 'Не общие новости, а темы, где доступ к китайской информации даёт реальное преимущество покупателю, инженеру, врачу, исследователю или закупщику.' : 'Each desk connects reporting to a practical decision while keeping language and evidence boundaries visible.' ); ?></p>
    </div>
    <div class="fyz-desk-grid">
      <?php foreach ( $desks as $desk ) : ?>
        <?php $url = ! empty( $desk['spec'] ) ? fyzsxnb_home_target( $desk['spec'] ) : null; ?>
        <article class="fyz-desk">
          <?php if ( ! empty( $desk['meta'] ) ) : ?><span class="fyz-meta"><?php echo esc_html( $desk['meta'] ); ?></span><?php endif; ?>
          <h3><?php echo esc_html( isset( $desk['title'] ) ? $desk['title'] : '' ); ?></h3>
          <?php if ( ! empty( $desk['p'] ) ) : ?><p><?php echo esc_html( $desk['p'] ); ?></p><?php endif; ?>
          <?php if ( $url && ! empty( $desk['cta'] ) ) : ?>
            <a class="fyz-arrow" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $desk['cta'] ); ?> <span aria-hidden="true">→</span></a>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>