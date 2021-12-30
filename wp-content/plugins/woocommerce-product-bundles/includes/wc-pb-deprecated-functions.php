<?php
/**
 * Product Bundles deprecated global functions
 *
 * @package  WooCommerce Product Bundles
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_bundles_dropdown_variation_attribute_options( $args = array() ) {
	_deprecated_function( __FUNCTION__ . '()', '4.13.1', 'wc_dropdown_variation_attribute_options()' );
	return wc_dropdown_variation_attribute_options( $args );
}

function wc_bundles_get_price_decimals() {
	_deprecated_function( __FUNCTION__ . '()', '4.13.1', 'wc_get_price_decimals()' );
	return wc_get_price_decimals();
}

function wc_bundles_get_product_terms( $product_id, $attribute_name, $args ) {
	_deprecated_function( __FUNCTION__ . '()', '4.13.1', 'wc_get_product_terms()' );
	return wc_get_product_terms( $product_id, $attribute_name, $args );
}

function wc_bundles_attribute_label( $arg ) {
	_deprecated_function( __FUNCTION__ . '()', '4.8.0', 'wc_attribute_label()' );
	return wc_attribute_label( $arg );
}

function wc_bundles_attribute_order_by( $arg ) {
	_deprecated_function( __FUNCTION__ . '()', '4.8.0', 'wc_attribute_orderby()' );
	return wc_attribute_orderby( $arg );
}

function wc_bundles_get_template( $file, $data, $empty, $path ) {
	_deprecated_function( __FUNCTION__ . '()', '4.8.0', 'wc_get_template()' );
	return wc_get_template( $file, $data, $empty, $path );
}
