<?php
/**
 * Composited Product Quantity template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/quantity.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 3.2.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

ob_start();

woocommerce_quantity_input( array(
	'input_name'  => 'wccp_component_quantity[' . $component_id . ']',
	'min_value'   => $quantity_min,
	'max_value'   => $quantity_max,
	'input_value' => isset( $_REQUEST[ 'wccp_component_quantity' ][ $component_id ] ) ? wc_clean( $_REQUEST[ 'wccp_component_quantity' ][ $component_id ] ) : apply_filters( 'woocommerce_composited_product_quantity', $quantity_min, $quantity_min, $quantity_max, $product, $component_id, $composite_product )
), $product );

$quantity_input = ob_get_clean();

if ( $quantity_max !== '' && $quantity_min == $quantity_max ) {
	echo preg_replace( '/(class=\"[^\"]*quantity)([\"\ ])/', '$1 quantity_hidden$2', $quantity_input );
} else {
 	echo $quantity_input;
}
