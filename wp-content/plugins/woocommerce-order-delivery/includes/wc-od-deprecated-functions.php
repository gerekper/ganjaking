<?php
/**
 * Deprecated functions
 *
 * @package WC_OD/Functions
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the plugin prefix.
 *
 * Note: The prefix is used for the settings Ids.
 *
 * @since 1.1.0
 * @deprecated 1.7.0
 *
 * @return string The plugin prefix.
 */
function wc_od_get_prefix() {
	wc_deprecated_function( __FUNCTION__, '1.7.0' );

	return 'wc_od_';
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.5.0
 * @deprecated 1.7.0
 *
 * @param string $string String to convert.
 * @return bool
 */
function wc_od_string_to_bool( $string ) {
	wc_deprecated_function( __FUNCTION__, '1.7.0', 'wc_string_to_bool' );

	return wc_string_to_bool( $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.5.0
 * @deprecated 1.7.0
 *
 * @param bool $bool String to convert.
 * @return string
 */
function wc_od_bool_to_string( $bool ) {
	wc_deprecated_function( __FUNCTION__, '1.7.0', 'wc_bool_to_string' );

	return wc_bool_to_string( $bool );
}

/**
 * Gets the logger instance.
 *
 * @since 1.4.0
 * @deprecated 1.7.0
 *
 * @return WC_Logger
 */
function wc_od_get_logger() {
	wc_deprecated_function( __FUNCTION__, '1.7.0', 'wc_get_logger' );

	return wc_get_logger();
}

/**
 * Get an array of checkout fields.
 *
 * @since 1.5.0
 * @deprecated 1.7.0
 *
 * @param string $fieldset Optional. The fieldset to get.
 * @return array
 */
function wc_od_get_checkout_fields( $fieldset = '' ) {
	wc_deprecated_function( __FUNCTION__, '1.7.0', 'WC_Checkout->get_checkout_fields()' );

	$checkout = WC()->checkout();

	return $checkout->get_checkout_fields( $fieldset );
}

/**
 * Gets a property from the order.
 *
 * @since 1.1.0
 * @since 1.6.0 Added support for 'currency' property.
 * @deprecated 1.7.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key      Name of prop to get.
 * @return mixed|null The prop value. Null on failure.
 */
function wc_od_get_order_prop( $the_order, $key ) {
	wc_deprecated_function( __FUNCTION__, '1.7.0', "WC_Order->get_{$key}()" );

	$order = wc_od_get_order( $the_order );

	if ( ! $order ) {
		return null;
	}

	$getter = array( $order, "get_{$key}" );

	// Properties renamed in WC 3.0+.
	$renamed_props = array(
		'date_created' => 'order_date',
		'currency'     => 'order_currency',
	);

	if ( is_callable( $getter ) ) {
		$prop = call_user_func( $getter );
	} else {
		$key  = ( array_key_exists( $key, $renamed_props ) ? $renamed_props[ $key ] : $key );
		$prop = $order->{$key};
	}

	return $prop;
}
