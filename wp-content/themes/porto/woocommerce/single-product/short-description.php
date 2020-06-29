<?php
/**
 * Single product short description
 *
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

if ( ! $short_description ) {
	return;
}
?>

<div class="description woocommerce-product-details__short-description">
	<?php echo ! $short_description ? '' : $short_description; // WPCS: XSS ok. ?>
</div>
