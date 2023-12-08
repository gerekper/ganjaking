<?php
/**
 * The template for displaying the product availability
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $current_product ) || ! $current_product || $current_product->is_type( 'variable' ) ) {
	return;
}

$availability = $current_product->get_availability();

if ( ! empty( $availability['availability'] ) ) {
	wc_get_template(
		'single-product/stock.php',
		[
			'product'      => $current_product,
			'class'        => $availability['class'],
			'availability' => $availability['availability'],
		]
	);
}
