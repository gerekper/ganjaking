<?php
/**
 * Display availability.
 * 
 * Primarily used for "Out of stock" text
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Availability
$availability      = $product->get_availability();
$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';

echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );