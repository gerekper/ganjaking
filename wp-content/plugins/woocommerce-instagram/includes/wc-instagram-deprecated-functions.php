<?php
/**
 * Deprecated functions
 *
 * @package WC_Instagram/Functions
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets a property from the product.
 *
 * @since 1.1.0
 * @deprecated 3.0.0
 *
 * @param mixed  $the_product Post object or post ID of the product.
 * @param string $key         Name of prop to get.
 * @return mixed|null The prop value. Null on failure.
 */
function wc_instagram_get_product_prop( $the_product, $key ) {
	wc_deprecated_function( __FUNCTION__, '3.0', "WC_Product::get_{$key}" );

	$product = ( $the_product instanceof WC_Product ? $the_product : wc_get_product( $the_product ) );

	if ( ! $product ) {
		return null;
	}

	$callable = array( $product, "get_{$key}" );

	return ( is_callable( $callable ) ? call_user_func( $callable ) : $product->$key );
}

/**
 * Gets the logger instance.
 *
 * @since 2.0.0
 * @deprecated 3.0.0
 *
 * @return WC_Logger
 */
function wc_instagram_get_logger() {
	wc_deprecated_function( __FUNCTION__, '3.0', 'wc_get_logger' );

	return wc_get_logger();
}

/**
 * Gets the expiration time for the transient used to cache the API requests.
 *
 * @since 2.0.0
 * @deprecated 3.0.0
 *
 * @param string $context The context.
 * @return int
 */
function wc_instragram_get_transient_expiration_time( $context = '' ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'wc_instagram_get_transient_expiration_time' );

	return wc_instagram_get_transient_expiration_time( $context );
}

/**
 * Processes the AJAX request for generating the product catalog slug.
 *
 * @since 3.0.0
 * @deprecated 3.4.6
 */
function wc_instagram_ajax_generate_product_catalog_slug() {
	wc_deprecated_function( __FUNCTION__, '3.4.6', 'WC_Instagram_AJAX::generate_product_catalog_slug()' );

	WC_Instagram_AJAX::generate_product_catalog_slug();
}
