<?php
/**
 * Deprecated functions
 *
 * @package WC_Store_Credit/Functions
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets if the option to apply the discount before taxes is available or not.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @return bool
 */
function wc_store_credit_allow_before_tax_option() {
	wc_deprecated_function( __FUNCTION__, '3.0' );

	return wc_tax_enabled();
}

/**
 * Gets the cart total.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @param mixed $cart Optional. The WC_Cart instance.
 * @return float
 */
function wc_store_credit_get_cart_total( $cart = null ) {
	wc_deprecated_function( __FUNCTION__, '3.0', "WC()->cart->get_total( 'edit' )" );

	if ( is_null( $cart ) ) {
		$cart = WC()->cart;
	}

	return $cart->get_total( 'edit' );
}

/**
 * Sets the cart total.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @param float $total The total amount.
 * @param mixed $cart Optional. The WC_Cart instance.
 */
function wc_store_credit_set_cart_total( $total, $cart = null ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC()->cart->set_total' );

	if ( is_null( $cart ) ) {
		$cart = WC()->cart;
	}

	$cart->set_total( $total );
}

/**
 * Gets the coupon discount totals.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @return array
 */
function wc_store_credit_get_coupon_discount_totals() {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC()->cart->get_coupon_discount_totals' );

	return WC()->cart->get_coupon_discount_totals();
}

/**
 * Sets the coupon discount totals.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @param array $coupon_discount_totals The total discounts.
 */
function wc_store_credit_set_coupon_discount_totals( $coupon_discount_totals ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC()->cart->set_coupon_discount_totals' );

	WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
}

/**
 * Gets if the coupon can be applied to the item or not.
 *
 * @since 2.4.0
 * @deprecated 3.0.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @param mixed $item       The item to apply the coupon.
 * @return bool
 */
function wc_store_credit_is_valid_coupon_for_item( $the_coupon, $item ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC_Coupon::is_valid_for_product' );

	$coupon = wc_store_credit_get_coupon( $the_coupon );

	if ( $item instanceof WC_Order_Item ) {
		$product  = $item->get_product();
		$quantity = $item->get_quantity();
	} else {
		$product  = $item['data'];
		$quantity = $item['quantity'];
	}

	$valid = (
		$coupon &&
		0 < $quantity &&
		( $coupon->is_valid_for_cart() || $coupon->is_valid_for_product( $product, $item ) )
	);

	if ( has_filter( 'wc_store_credit_is_valid_coupon_for_item' ) ) {
		wc_deprecated_hook( 'wc_store_credit_is_valid_coupon_for_item', '3.0', 'woocommerce_coupon_is_valid_for_product' );

		/**
		 * Filters if the coupon can be applied to the item.
		 *
		 * @since 2.4.0
		 * @deprecated 3.0.0
		 *
		 * @param bool      $valid  True if can be applied. False otherwise.
		 * @param WC_Coupon $coupon The coupon object.
		 * @param mixed     $item   The item to apply the coupon.
		 */
		$valid = apply_filters( 'wc_store_credit_is_valid_coupon_for_item', $valid, $coupon, $item );
	}

	return $valid;
}

/**
 * Gets a coupon property.
 *
 * @since 2.2.0
 * @deprecated 3.0.0
 *
 * @param mixed  $the_coupon Coupon object, ID or code.
 * @param string $key        Coupon property.
 * @return mixed Value of coupon property. Null on failure.
 */
function wc_store_credit_get_coupon_prop( $the_coupon, $key ) {
	if ( 'type' === $key ) {
		$key = 'discount_type';
	}

	wc_deprecated_function( __FUNCTION__, '3.0', "WC_Coupon::get_{$key}" );

	$coupon = wc_store_credit_get_coupon( $the_coupon );

	if ( ! $coupon ) {
		return null;
	}

	$getter = array( $coupon, 'get_' . $key );

	return ( is_callable( $getter ) ? call_user_func( $getter ) : $coupon->{ $key } );
}

/**
 * Gets an order property.
 *
 * @since 2.4.0
 * @since 2.4.4 Added support for 'currency' property.
 * @deprecated 3.0.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       Order property.
 * @return mixed Value of order property. Null on failure.
 */
function wc_store_credit_get_order_prop( $the_order, $key ) {
	wc_deprecated_function( __FUNCTION__, '3.0', "WC_Order::get_{$key}" );

	$order = wc_store_credit_get_order( $the_order );

	if ( ! $order ) {
		return null;
	}

	$getter = array( $order, "get_{$key}" );

	// Properties renamed in WC 3.0+.
	$renamed_props = array(
		'date_created' => 'order_date',
		'currency'     => 'order_currency',
	);

	$key = ( array_key_exists( $key, $renamed_props ) ? $renamed_props[ $key ] : $key );

	return ( is_callable( $getter ) ? call_user_func( $getter ) : $order->{$key} );
}

/**
 * Updates an order meta data by key.
 *
 * @since 2.4.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       The meta key to update.
 * @param mixed  $value     The meta value.
 * @return bool True on successful update, false on failure.
 */
function wc_store_credit_update_order_meta( $the_order, $key, $value ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC_Order::update_meta_data' );

	$is_object = ( $the_order instanceof WC_Order );

	if ( $is_object && method_exists( $the_order, 'update_meta_data' ) ) {
		$the_order->update_meta_data( $key, $value );
	}

	$order_id = ( $is_object ? $the_order->get_id() : intval( $the_order ) );

	return (bool) update_post_meta( $order_id, $key, $value );
}

/**
 * Deletes an order meta data by key.
 *
 * @since 2.4.0
 * @deprecated 3.0.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       The meta key to update.
 * @return bool True on successful delete, false on failure.
 */
function wc_store_credit_delete_order_meta( $the_order, $key ) {
	wc_deprecated_function( __FUNCTION__, '3.0', 'WC_Order::delete_meta_data' );

	$is_object = ( $the_order instanceof WC_Order );

	if ( $is_object && method_exists( $the_order, 'delete_meta_data' ) ) {
		$the_order->delete_meta_data( $key );
	}

	$order_id = ( $is_object ? $the_order->get_id() : intval( $the_order ) );

	return (bool) delete_post_meta( $order_id, $key );
}

/**
 * Gets the logger instance.
 *
 * @since 2.4.0
 * @deprecated 3.0.0
 *
 * @return WC_Logger
 */
function wc_store_credit_get_logger() {
	wc_deprecated_function( __FUNCTION__, '3.0', 'wc_get_logger' );

	return wc_get_logger();
}

/**
 * Removes a store credit coupon from the specified order.
 *
 * @since 2.4.0
 * @deprecated 3.0.0
 *
 * @param mixed $the_order  Order object or ID.
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return bool True if the coupon was removed. False otherwise.
 */
function wc_remove_store_credit_coupon_from_order( $the_order, $the_coupon ) {
	wc_deprecated_function( __FUNCTION__, '3.0' );

	$credit_used = wc_get_store_credit_used_for_order( $the_order, 'per_coupon' );
	$coupon_code = wc_store_credit_get_coupon_code( $the_coupon );

	if ( $coupon_code && isset( $credit_used[ $coupon_code ] ) ) {
		// Restore the coupon credit.
		wc_update_store_credit_coupon_balance( $coupon_code, $credit_used[ $coupon_code ], 'increase' );

		// Remove the coupon from the credit used.
		unset( $credit_used[ $coupon_code ] );

		// Update the credit used.
		return wc_update_store_credit_used_for_order( $the_order, $credit_used );
	}

	return false;
}
