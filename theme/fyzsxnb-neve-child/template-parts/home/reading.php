<?php
/**
 * Wider reading (optional; EN only for now).
 *
 * @package FYZSXNB_Neve_Child
 */
$cfg = isset( $args['cfg'] ) ? $args['cfg'] : array();
$reading = isset( $cfg['reading'] ) ? $cfg['reading'] : array();
$cards = isset( $reading['cards'] ) ? $reading['cards'] : array();
if ( empty( $cards ) ) {
	return;
}
$resolved = array();
foreach ( $cards as $card ) {
	$post = get_page_by_path( isset( $card['slug'] ) ? $card['slug'] : '', OBJECT, 'post' );
	if ( ! $post || 'publish' !== $post->post_status ) {
		continue;
	}
	$resolved[] = array(
		'label'    => isset( $card['label'] ) ? $card['label'] : '',
		'p'        => isset( $card['p'] ) ? $card['p'] : '',
		'url'      => get_permalink( $post ),
		'img'      => get_the_post_thumbnail_url( $post, 'large' ),
		'alt'      => get_the_title( $post ),
		'title'    => get_the_title( $post ),
		'document' => ! empty( $card['document'] ),
	);
}
if ( empty( $resolved ) ) {
	return;
}
$links = isset( $reading['links'] ) ? $reading['links'] : array();
?>
<section class="fyz-band fyz-reading" <?php echo ! empty( $reading['section_id'] ) ? 'id="' . esc_attr( $reading['section_id'] ) . '"' : ''; ?> aria-labelledby="fyz-reading-title">
  <div class="fyz-inner">
    <div class="fyz-reading__head">
      <div>
        <?php if ( ! empty( $reading['eyebrow'] ) ) : ?><p class="fyz-eyebrow"><?php echo esc_html( $reading['eyebrow'] ); ?></p><?php endif; ?>
        <h2 id="fyz-reading-title"><?php echo esc_html( isset( $reading['title'] ) ? $reading['title'] : '' ); ?></h2>
      </div>
    </div>
    <div class="fyz-reading-grid">
      <?php foreach ( $resolved as $c ) : ?>
        <article class="fyz-reading-card<?php echo $c['document'] ? ' fyz-reading--document' : ''; ?>">
          <?php if ( $c['img'] ) : ?><a class="fyz-reading__media" href="<?php echo esc_url( $c['url'] ); ?>"><img src="<?php echo esc_url( $c['img'] ); ?>" alt="<?php echo esc_attr( $c['alt'] ); ?>" loading="lazy" decoding="async" /></a><?php endif; ?>
          <?php if ( $c['label'] ) : ?><span class="fyz-meta"><?php echo esc_html( $c['label'] ); ?></span><?php endif; ?>
          <h3><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['title'] ); ?></a></h3>
          <p><?php echo esc_html( $c['p'] ); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
    <?php if ( $links ) : ?>
      <nav class="fyz-reading-links" aria-label="<?php echo esc_attr( isset( $reading['title'] ) ? $reading['title'] : 'Archive' ); ?>">
        <?php foreach ( $links as $l ) : ?>
          <?php $url = fyzsxnb_home_target( $l['spec'] ); if ( $url ) : ?>
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>