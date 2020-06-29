<?php
/**
 * Backward compatibility functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets a property from the order.
 *
 * @since 1.1.0
 * @since 1.6.0 Added support for 'currency' property.
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key      Name of prop to get.
 * @return mixed|null The prop value. Null on failure.
 */
function wc_od_get_order_prop( $the_order, $key ) {
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

/**
 * Gets an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       Optional. The meta key to retrieve.
 * @param bool   $single    Optional. Whether to return a single value. Default true.
 * @return mixed The meta data value.
 */
function wc_od_get_order_meta( $the_order, $key = '', $single = true ) {
	$meta = '';

	$order_id = ( $the_order instanceof WC_Order ? wc_od_get_order_prop( $the_order, 'id' ) : intval( $the_order ) );

	if ( $order_id ) {
		$meta = get_post_meta( $order_id, $key, $single );
	}

	return $meta;
}

/**
 * Updates an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       The meta key to update.
 * @param mixed  $value     The meta value.
 * @param bool   $save      Optional. True to save the meta immediately. Default false.
 * @return bool True on successful update, false on failure.
 */
function wc_od_update_order_meta( $the_order, $key, $value, $save = false ) {
	$updated   = false;
	$is_object = ( $the_order instanceof WC_Order );

	if ( $is_object && method_exists( $the_order, 'update_meta_data' ) ) {
		$old_value = $the_order->get_meta( $key );

		if ( $old_value !== $value ) {
			$the_order->update_meta_data( $key, $value );
			$updated = true;

			// Save the meta immediately.
			if ( $save ) {
				$the_order->save_meta_data();
			}
		}
	} else {
		$order_id = ( $is_object ? wc_store_credit_get_order_prop( $the_order, 'id' ) : intval( $the_order ) );
		$updated  = (bool) update_post_meta( $order_id, $key, $value );
	}

	return $updated;
}

/**
 * Deletes an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       The meta key to delete.
 * @param bool   $save      Optional. True to delete the meta immediately. Default false.
 * @return bool True on successful delete, false on failure.
 */
function wc_od_delete_order_meta( $the_order, $key, $save = false ) {
	$is_object = ( $the_order instanceof WC_Order );

	if ( $is_object && method_exists( $the_order, 'delete_meta_data' ) ) {
		$the_order->delete_meta_data( $key );
		$deleted = true;

		// Save the meta immediately.
		if ( $save ) {
			$the_order->save_meta_data();
		}
	} else {
		$order_id = ( $is_object ? wc_store_credit_get_order_prop( $the_order, 'id' ) : intval( $the_order ) );
		$deleted  = delete_post_meta( $order_id, $key );
	}

	return $deleted;
}

/**
 * Gets the logger instance.
 *
 * @since 1.4.0
 *
 * @return WC_Logger
 */
function wc_od_get_logger() {
	return ( function_exists( 'wc_get_logger' ) ? wc_get_logger() : new WC_Logger() );
}

/**
 * Logs a message.
 *
 * @since 1.4.0
 *
 * @param string         $message The message to log.
 * @param string         $level   The level.
 * @param string         $handle  Optional. The log handlers.
 * @param WC_Logger|null $logger  Optional. The logger instance.
 */
function wc_od_log( $message, $level = 'notice', $handle = 'wc_od', $logger = null ) {
	if ( ! $logger ) {
		$logger = wc_od_get_logger();
	}

	if ( method_exists( $logger, $level ) ) {
		call_user_func( array( $logger, $level ), $message, array( 'source' => $handle ) );
	} else {
		$logger->add( $handle, $message );
	}
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.5.0
 *
 * @param string $string String to convert.
 * @return bool
 */
function wc_od_string_to_bool( $string ) {
	// TODO: Use 'wc_string_to_bool' function when the minimum requirements are WC 3.0+
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.5.0
 *
 * @param bool $bool String to convert.
 * @return string
 */
function wc_od_bool_to_string( $bool ) {
	// TODO: Use 'wc_bool_to_string' function when the minimum requirements are WC 3.0+
	if ( ! is_bool( $bool ) ) {
		$bool = wc_od_string_to_bool( $bool );
	}

	return ( true === $bool ? 'yes' : 'no' );
}

/**
 * Get an array of checkout fields.
 *
 * @since 1.5.0
 *
 * @param string $fieldset Optional. The fieldset to get.
 * @return array
 */
function wc_od_get_checkout_fields( $fieldset = '' ) {
	$checkout = WC()->checkout();

	// Added in WC 3.0.
	if ( method_exists( $checkout, 'get_checkout_fields' ) ) {
		$fields = $checkout->get_checkout_fields( $fieldset );
	} else {
		$fields = ( $fieldset ? $checkout->checkout_fields[ $fieldset ] : $checkout->checkout_fields );
	}

	return $fields;
}
