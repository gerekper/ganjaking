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
	$order = wc_od_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	$delivery_date = $order->get_meta( '_delivery_date' );

	// No delivery date or expired.
	if ( ! $delivery_date || $delivery_date < wc_od_get_local_date( false ) ) {
		return false;
	}

	$args = array(
		'delivery_date'               => $delivery_date,
		'shipping_method'             => wc_od_get_order_shipping_method( $order ),
		'disabled_delivery_days_args' => array(
			'type'    => 'delivery',
			'country' => $order->get_shipping_country(),
			'state'   => $order->get_shipping_state(),
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
	$args = apply_filters( 'wc_od_get_order_last_shipping_date_args', $args, $order->get_id(), $context );

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

/**
 * Retrieves the number of orders to be delivered in a certain date.
 *
 * @since 1.8.0
 *
 * @param int $timestamp The timestamp.
 * @return int
 */
function wc_od_get_orders_to_deliver( $timestamp ) {
	/** @var WC_OD_Delivery_Cache $delivery_cache */
	$delivery_cache = WC_OD_Delivery_Cache::instance();
	$cache_key      = $delivery_cache->build_cache_key( WC_OD_Delivery_Cache::ORDER_CACHE_PREFIX, array( date( 'Y-m-d', $timestamp ) ) );
	$cache_data     = $delivery_cache->read( $cache_key );

	if ( false !== $cache_data && is_numeric( $cache_data ) ) {
		return intval( $cache_data );
	}

	$order_ids = get_posts(
		array(
			'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-processing', 'wc-on-hold', 'wc-completed' ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_delivery_date',
					'value' => gmdate( 'Y-m-d', $timestamp ),
				),
				array(
					'key'     => '_delivery_time_frame',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	$count = count( $order_ids );

	$delivery_cache->write( $cache_key, $count );

	return $count;
}

/**
 * Retrieves the number of orders to be delivered in a certain date and time frame.
 *
 * @since 1.8.0
 *
 * @param int    $timestamp Timestamp.
 * @param string $from      Time from.
 * @param string $to        Time to.
 * @return int
 */
function wc_od_get_orders_to_deliver_in_time_frame( $timestamp, $from, $to ) {
	/** @var WC_OD_Delivery_Cache $delivery_cache */
	$delivery_cache = WC_OD_Delivery_Cache::instance();
	$cache_key      = $delivery_cache->build_cache_key( WC_OD_Delivery_Cache::ORDER_CACHE_PREFIX, array( date( 'Y-m-d', $timestamp ), $from, $to ) );
	$cache_data     = $delivery_cache->read( $cache_key );

	if ( false !== $cache_data && is_numeric( $cache_data ) ) {
		return intval( $cache_data );
	}

	$order_ids = get_posts(
		array(
			'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-processing', 'wc-on-hold', 'wc-completed' ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_delivery_date',
					'value' => gmdate( 'Y-m-d', $timestamp ),
				),
				array(
					'key'     => '_delivery_time_frame',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$count = 0;

	if ( ! empty( $order_ids ) ) {
		$time_from = strtotime( $from, $timestamp );
		$time_to   = strtotime( $to, $timestamp );

		foreach ( $order_ids as $order_id ) {
			$time_frame = get_post_meta( $order_id, '_delivery_time_frame', true );

			if ( ! is_array( $time_frame ) ) {
				continue;
			}

			$order_time_from = strtotime( $time_frame['time_from'], $timestamp );
			$order_time_to   = strtotime( $time_frame['time_to'], $timestamp );

			if (
				( $order_time_from === $time_from && $order_time_to === $time_to ) || // The same range.
				( $order_time_from < $time_to && $order_time_to > $time_from ) // The two time ranges intersect.
			) {
				$count ++;
			}
		}
	}

	$delivery_cache->write( $cache_key, $count );

	return $count;
}
