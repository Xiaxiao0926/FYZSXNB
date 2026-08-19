<?php
/**
 * Research CTA band.
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$cta = isset( $cfg['cta'] ) ? $cfg['cta'] : array();
if ( empty( $cta ) || empty( $cta['title'] ) ) {
	return;
}
$url = ! empty( $cta['spec'] ) ? fyzsxnb_home_target( $cta['spec'] ) : null;
if ( ! $url ) {
	return;
}
?>
<section class="fyz-band fyz-cta-band" aria-label="<?php echo esc_attr( isset( $cta['title'] ) ? $cta['title'] : 'Contact the research desk' ); ?>">
  <div class="fyz-inner fyz-cta">
    <div>
      <h2><?php echo esc_html( $cta['title'] ); ?></h2>
      <?php if ( ! empty( $cta['p'] ) ) : ?><p><?php echo esc_html( $cta['p'] ); ?></p><?php endif; ?>
    </div>
    <a class="fyz-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( isset( $cta['label'] ) ? $cta['label'] : 'Contact' ); ?></a>
  </div>
</section>