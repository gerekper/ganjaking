<?php
/**
 * Composite Products Deprecated Functions
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.5.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_cp_get_product_terms( $product_id, $attribute_name, $args ) {
	_deprecated_function( 'wc_cp_get_product_terms()', '3.5.2', 'wc_get_product_terms()' );
	return wc_get_product_terms( $product_id, $attribute_name, $args );
}

function wc_cp_get_variation_default_attribute( $product, $attribute_name ) {
	_deprecated_function( 'wc_cp_get_variation_default_attribute()', '3.5.2', 'WC_Product::get_variation_default_attribute()' );
	return $product->get_variation_default_attribute( $attribute_name );
}

function wc_cp_dropdown_variation_attribute_options( $args = array() ) {
	_deprecated_function( 'wc_cp_dropdown_variation_attribute_options()', '3.5.2', 'wc_dropdown_variation_attribute_options()' );
	return wc_dropdown_variation_attribute_options( $args );
}

function wc_composite_get_template( $file, $data, $empty, $path ) {
	_deprecated_function( 'wc_composite_get_template()', '3.5.0', 'wc_get_template()' );
	return wc_get_template( $file, $data, $empty, $path );
}

function wc_composite_get_product_terms( $product_id, $attribute_name, $args ) {
	_deprecated_function( 'wc_composite_get_product_terms()', '3.5.0', 'wc_get_product_terms()' );
	return wc_get_product_terms( $product_id, $attribute_name, $args );
}

function wc_composite_get_variation_default_attribute( $product, $attribute_name ) {
	_deprecated_function( 'wc_cp_get_variation_default_attribute()', '3.5.0', 'WC_Product::get_variation_default_attribute()' );
	return $product->get_variation_default_attribute( $attribute_name );
}

function wc_composite_dropdown_variation_attribute_options( $args = array() ) {
	_deprecated_function( 'wc_composite_dropdown_variation_attribute_options()', '3.5.0', 'wc_dropdown_variation_attribute_options()' );
	return wc_dropdown_variation_attribute_options( $args );
}

function wc_composite_tax_display_shop() {
	_deprecated_function( 'wc_composite_tax_display_shop()', '3.5.0', 'wc_cp_tax_display_shop()' );
	return wc_cp_tax_display_shop();
}

function wc_composite_price_decimal_sep() {
	_deprecated_function( 'wc_composite_price_decimal_sep()', '3.5.0', 'wc_cp_price_decimal_sep()' );
	return wc_cp_price_decimal_sep();
}

function wc_composite_price_thousand_sep() {
	_deprecated_function( 'wc_composite_price_thousand_sep()', '3.5.0', 'wc_cp_price_thousand_sep()' );
	return wc_cp_price_thousand_sep();
}

function wc_composite_price_num_decimals() {
	_deprecated_function( 'wc_composite_price_num_decimals()', '3.5.0', 'wc_cp_price_num_decimals()' );
	return wc_cp_price_num_decimals();
}
