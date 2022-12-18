<?php
/**
 * Single Product title
 *
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $porto_settings;
?>
<h1 class="product_title entry-title<?php echo ( apply_filters( 'porto_legacy_mode', true ) && ! $porto_settings['product-nav'] ) ? '' : ' show-product-nav'; ?>">
	<?php if ( porto_is_ajax() ) : ?>
	<a href="<?php the_permalink(); ?>">
	<?php endif; ?>
	<?php the_title(); ?>
	<?php if ( porto_is_ajax() ) : ?>
	</a>
	<?php endif; ?>
</h1>