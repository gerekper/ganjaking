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
 * Retrieves the number of orders to be delivered in a certain date (timestamp).
 *
 * @param string $timestamp Date to be checked.
 *
 * @return int
 */
function wc_od_get_orders_to_deliver( $timestamp ) {
	global $wpdb;

	/** @var WC_OD_Delivery_Cache $delivery_cache */
	$delivery_cache = WC_OD_Delivery_Cache::instance();
	$cache_key      = $delivery_cache->build_cache_key( WC_OD_Delivery_Cache::ORDER_CACHE_PREFIX, array( date( 'Y-m-d', $timestamp ) ) );
	$cache_data     = $delivery_cache->read( $cache_key );

	if ( false !== $cache_data && is_numeric( $cache_data ) ) {
		return intval( $cache_data );
	}

	$result = $wpdb->get_var(
		$wpdb->prepare(
			"
			SELECT COUNT(pm.meta_id) 
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p
			ON p.ID = pm.post_id
			INNER JOIN {$wpdb->prefix}wc_order_stats wcos
		 	ON pm.post_id = wcos.order_id
		 	WHERE p.post_type = 'shop_order'
			AND p.post_status != 'trash' 
			AND pm.meta_key = '_delivery_date' AND pm.meta_value = %s
			AND wcos.status NOT IN ('wc-cancelled', 'wc-refunded', 'wc-failed')
			",
			date( 'Y-m-d', $timestamp )
		)
	);

	$total = 0;
	if ( null !== $result ) {
		$total = intval( $result );
	}

	$delivery_cache->write( $cache_key, $total );

	return $total;
}

/**
 * Retrieves the number of orders to be delivered in a certain date and time frame.
 *
 * @todo Implement logic to filter the time frames (_delivery_time_frame).
 *
 * @param string $timestamp Timestamp of the date.
 * @param string $from      Time from.
 * @param string $to        Time to.
 *
 * @return int
 */
function wc_od_get_orders_to_deliver_in_time_frame( $timestamp, $from, $to ) {
	global $wpdb;

	/** @var WC_OD_Delivery_Cache $delivery_cache */
	$delivery_cache = WC_OD_Delivery_Cache::instance();
	$cache_key      = $delivery_cache->build_cache_key( WC_OD_Delivery_Cache::ORDER_CACHE_PREFIX, array( date( 'Y-m-d', $timestamp ), $from, $to ) );
	$cache_data     = $delivery_cache->read( $cache_key );

	if ( false !== $cache_data && is_numeric( $cache_data ) ) {
		return intval( $cache_data );
	}

	$results = $wpdb->get_results(
		$wpdb->prepare(
			"
			SELECT pm.post_id 
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p
			ON p.ID = pm.post_id
			INNER JOIN {$wpdb->prefix}wc_order_stats wcos
		 	ON pm.post_id = wcos.order_id
			WHERE p.post_type = 'shop_order' 
			AND p.post_status != 'trash' 
			AND pm.meta_key = '_delivery_date' AND pm.meta_value = %s
			AND wcos.status NOT IN ('wc-cancelled', 'wc-refunded', 'wc-failed')
			",
			date( 'Y-m-d', $timestamp )
		)
	);

	$count = 0;
	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			$order      = wc_od_get_order( $result->post_id );
			$time_frame = $order->get_meta( '_delivery_time_frame' );

			if ( '' === $time_frame ) {
				continue;
			}

			$order_time_from = date( 'H:i', strtotime( $time_frame['time_from'] ) );
			$order_time_to   = date( 'H:i', strtotime( $time_frame['time_to'] ) );

			/*
			 * @todo Check if _delivery_time_frame should be updated when admin changes time frames.
			 * How do we know between which time frame the order is if the admin changes the configuration of
			 * the time frames?
			 * Ex: Monday -> 08:00 to 12:00. Then changed to 08:00 to 10:00.
			 * Orders placed using 08:00 to 12:00 will not be counted because the $order_time_from (12:00) is greater
			 * than the new $to (10:00).
			 */
			if ( $order_time_from >= $from && $order_time_to <= $to ) {
				$count++;
			}
		}
	}

	$delivery_cache->write( $cache_key, $count );

	return $count;
}
