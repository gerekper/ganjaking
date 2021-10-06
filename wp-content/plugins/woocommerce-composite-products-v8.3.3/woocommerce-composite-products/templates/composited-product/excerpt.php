<?php
/**
 * Composited Product Excerpt template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/excerpt.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $product_description ) {
	echo apply_filters( 'woocommerce_composited_product_excerpt', wpautop( do_shortcode( wp_kses_post( $product_description ) ) ), $product_id, $component_id, $composite );
}
