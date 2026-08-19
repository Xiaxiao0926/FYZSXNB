<?php
/**
 * Trust / method section (shared structure, per-locale copy).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$trust = isset( $cfg['trust'] ) ? $cfg['trust'] : array();
if ( empty( $trust ) || ( empty( $trust['title'] ) && empty( $trust['method'] ) ) ) {
	return;
}
$ru = 'ru-RU' === ( isset( $args['locale'] ) ? $args['locale'] : '' );
$steps = isset( $trust['steps'] ) ? $trust['steps'] : array();
$method = isset( $trust['method'] ) ? $trust['method'] : '';
$intro = isset( $trust['intro'] ) ? $trust['intro'] : '';
?>
<section class="fyz-band fyz-trust" <?php echo ! empty( $trust['section_id'] ) ? 'id="' . esc_attr( $trust['section_id'] ) . '"' : ''; ?> aria-labelledby="fyz-trust-title">
  <div class="fyz-inner fyz-trust__grid">
    <div>
      <?php if ( ! empty( $trust['eyebrow'] ) ) : ?><p class="fyz-eyebrow"><?php echo esc_html( $trust['eyebrow'] ); ?></p><?php endif; ?>
      <h2 id="fyz-trust-title"><?php echo esc_html( isset( $trust['title'] ) ? $trust['title'] : '' ); ?></h2>
      <?php if ( $intro ) : ?><p class="fyz-trust__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
      <?php if ( $method ) : ?>
        <div class="fyz-method"><p><?php echo esc_html( $method ); ?></p></div>
      <?php endif; ?>
      <?php if ( ! empty( $trust['notice'] ) ) : ?><p class="fyz-method__notice"><?php echo esc_html( $trust['notice'] ); ?></p><?php endif; ?>
    </div>
    <?php if ( $steps ) : ?>
      <ol class="fyz-trust-list">
        <?php foreach ( $steps as $step ) : ?>
          <li><strong><?php echo esc_html( isset( $step['s'] ) ? $step['s'] : '' ); ?></strong> <?php echo esc_html( isset( $step['d'] ) ? $step['d'] : '' ); ?></li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
</section>