<?php
/**
 * Order Functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the order instance.
 *
 * @since 1.6.0
 *
 * @param mixed $the_order Order object or ID.
 * @return false|WC_Order The WC_Order object. False on failure.
 */
function wc_od_get_order( $the_order ) {
	return ( $the_order instanceof WC_Order ? $the_order : wc_get_order( $the_order ) );
}

/**
 * Gets the last day to ship the order to receive it on time.
 *
 * @since 1.5.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $context   Optional. The context.
 * @return false|int A timestamp representing the last date to ship the order. False on failure.
 */
function wc_od_get_order_last_shipping_date( $the_order, $context = '' ) {
	$order_id = wc_od_get_order_prop( $the_order, 'id' );

	if ( ! $order_id ) {
		return false;
	}

	$delivery_date = wc_od_get_order_meta( $order_id, '_delivery_date' );

	// No delivery date or expired.
	if ( ! $delivery_date || $delivery_date < wc_od_get_local_date( false ) ) {
		return false;
	}

	$args = array(
		'delivery_date'               => $delivery_date,
		'shipping_method'             => wc_od_get_order_shipping_method( $order_id ),
		'disabled_delivery_days_args' => array(
			'type'    => 'delivery',
			'country' => wc_od_get_order_prop( $order_id, 'shipping_country' ),
			'state'   => wc_od_get_order_prop( $order_id, 'shipping_state' ),
		),
	);

	/**
	 * Filters the arguments used to calculate the last shipping date for the specified order.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $args     The arguments.
	 * @param int    $order_id The order ID.
	 * @param string $context  The context.
	 */
	$args = apply_filters( 'wc_od_get_order_last_shipping_date_args', $args, $order_id, $context );

	return wc_od_get_last_shipping_date( $args, $context );
}

/**
 * Checks if it's a request to save the specified order or not.
 *
 * The merchant is saving the specified order in the edit order screen.
 *
 * @since 1.5.5
 *
 * @param int $order_id The order ID.
 * @return bool
 */
function wc_od_is_save_request_for_order( $order_id ) {
	$save_order = false;

	// Check the nonce used for saving meta boxes and the post being saved.
	if (
		! empty( $_POST['woocommerce_meta_nonce'] ) && wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) && // phpcs:ignore sanitization ok.
		! empty( $_POST['post_ID'] ) && intval( wp_unslash( $_POST['post_ID'] ) ) === intval( $order_id )
	) {
		$save_order = true;
	}

	return $save_order;
}
