<?php
/**
 * Guides feed section (plugin backend unchanged).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$guides = isset( $args['guides'] ) ? $args['guides'] : array();
if ( empty( $guides ) || empty( $guides['title'] ) ) {
	return;
}
$locale = isset( $args['locale'] ) ? $args['locale'] : fyzsxnb_home_locale();
$feed = fyzsxnb_home_feed_html( $locale, 'guides' );
if ( '' === $feed ) {
	return;
}
$ru = 'ru-RU' === $locale;
$browse = isset( $guides['browse'] ) ? $guides['browse'] : null;
$browse_url = $browse ? fyzsxnb_home_target( $browse['spec'] ) : null;
?>
<section class="fyz-band fyz-guides" <?php echo ! empty( $guides['section_id'] ) ? 'id="' . esc_attr( $guides['section_id'] ) . '"' : ''; ?> aria-labelledby="fyz-guides-title">
  <div class="fyz-inner">
    <div class="fyz-guides__head">
      <div>
        <?php if ( ! empty( $guides['eyebrow'] ) ) : ?><p class="fyz-eyebrow"><?php echo esc_html( $guides['eyebrow'] ); ?></p><?php endif; ?>
        <h2 id="fyz-guides-title"><?php echo esc_html( $guides['title'] ); ?></h2>
      </div>
      <?php if ( $browse_url && ! empty( $browse['label'] ) ) : ?>
        <a class="fyz-arrow" href="<?php echo esc_url( $browse_url ); ?>"><?php echo esc_html( $browse['label'] ); ?> <span aria-hidden="true">→</span></a>
      <?php endif; ?>
    </div>
    <?php echo $feed; // phpcs:ignore WordPress.Security.EscapeOutput -- plugin's escaped markup ?>
  </div>
</section>