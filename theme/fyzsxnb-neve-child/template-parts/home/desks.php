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
        <p class="fyz-eyebrow"><?php echo esc_html( $ru ? 'Направления аналитики' : 'Intelligence Desks' ); ?></p>
        <h2 id="fyz-desks-title"><?php echo esc_html( $ru ? 'Практическая аналитика по ключевым направлениям' : 'Follow the question, not the category label' ); ?></h2>
      </div>
      <p><?php echo esc_html( $ru ? 'Инженерные разборы, регламенты обслуживания и проверенные данные от производителей для точных коммерческих и технических решений.' : 'Actionable technical intelligence, cross-border diagnostic standards, and verified supply chain insights for engineers, buyers, and industry professionals.' ); ?></p>
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