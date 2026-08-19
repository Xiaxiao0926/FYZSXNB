<?php
/**
 * Latest signals feed section (locale-pure, plugin backend unchanged).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg  = isset( $args['cfg'] ) ? $args['cfg'] : array();
$signals = isset( $args['signals'] ) ? $args['signals'] : array();
if ( empty( $signals ) || empty( $signals['title'] ) ) {
	return; // graceful: no signals data -> no section
}
$locale = isset( $args['locale'] ) ? $args['locale'] : fyzsxnb_home_locale();
$feed   = fyzsxnb_home_feed_html( $locale, 'signals' );
if ( '' === $feed ) {
	return;
}
$ru = 'ru-RU' === $locale;
?>
<section class="fyz-band fyz-signals" <?php echo ! empty( $signals['section_id'] ) ? 'id="' . esc_attr( $signals['section_id'] ) . '"' : ''; ?> aria-labelledby="fyz-signals-title">
  <div class="fyz-inner">
    <div class="fyz-signals__head">
      <h2 id="fyz-signals-title"><?php echo esc_html( $signals['title'] ); ?></h2>
      <?php if ( ! empty( $signals['note'] ) ) : ?><p><?php echo esc_html( $signals['note'] ); ?></p><?php endif; ?>
    </div>
    <?php echo $feed; // phpcs:ignore WordPress.Security.EscapeOutput -- plugin's escaped markup ?>
  </div>
</section>