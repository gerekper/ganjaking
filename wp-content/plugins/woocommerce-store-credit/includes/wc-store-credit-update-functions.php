<?php
/**
 * Functions for updating data, used by the background updater
 *
 * @package WC_Store_Credit/Functions
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Processes a large collection of items in batches.
 *
 * @since 3.0.0
 *
 * @param string   $option_name     The option name to fetch the items to process.
 * @param callable $callback        A callable function to process each item.
 * @param array    $args            Optional. Additional arguments to pass to the callback.
 * @param int      $items_per_batch Optional. Number of items to process per batch.
 * @return bool True to run it again, false if it's completed.
 */
function wc_store_credit_update_batch_process( $option_name, $callback, $args = array(), $items_per_batch = 50 ) {
	$items = (array) get_option( $option_name, array() );

	if ( empty( $items ) ) {
		return false;
	}

	// Process the items in batches.
	$batch = array_slice( $items, 0, $items_per_batch );

	foreach ( $batch as $item ) {
		call_user_func( $callback, $item, $args );
	}

	// Remove the processed items in this batch.
	$items = array_slice( $items, count( $batch ) );

	if ( ! empty( $items ) ) {
		return update_option( $option_name, $items );
	}

	delete_option( $option_name );

	return false;
}

/**
 * Stores the orders that need to synchronize the credit used.
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_store_credit_update_240_orders_to_sync_credit_used() {
	global $wpdb;

	// Fetch the orders with coupons.
	$orders_ids_with_coupons = $wpdb->get_col(
		"SELECT DISTINCT order_id
			 FROM {$wpdb->prefix}woocommerce_order_items
			 WHERE order_item_type = 'coupon'"
	);

	// Filter orders by type and status.
	$order_ids = wc_get_orders(
		array(
			'type'     => 'shop_order',
			'return'   => 'ids',
			'status'   => array( 'wc-pending', 'wc-on-hold', 'wc-processing', 'wc-completed' ),
			'limit'    => -1,
			'post__in' => $orders_ids_with_coupons,
		)
	);

	update_option( 'wc_store_credit_update_240_orders_to_sync_credit_used', $order_ids );
}

/**
 * Synchronizes the credit used by the orders.
 *
 * @return bool True to run again, false if it's completed.
 */
function wc_store_credit_update_240_sync_credit_used_by_orders() {
	return wc_store_credit_update_batch_process(
		'wc_store_credit_update_240_orders_to_sync_credit_used',
		'wc_store_credit_update_240_sync_credit_used_by_order'
	);
}

/**
 * Synchronizes the credit used by the order.
 *
 * @param int $order_id Order Id.
 */
function wc_store_credit_update_240_sync_credit_used_by_order( $order_id ) {
	$order = wc_store_credit_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$credit_used = wc_get_store_credit_used_for_order( $order, 'per_coupon' );

	// It's up to date.
	if ( ! empty( $credit_used ) ) {
		return;
	}

	// Fetch the 'store_credit' coupons.
	$coupons = wc_get_store_credit_coupons_for_order( $order );

	if ( ! empty( $coupons ) ) {
		$credit = array();

		foreach ( $coupons as $coupon ) {
			$credit[ $coupon->get_code( 'edit' ) ] = $coupon->get_discount( 'edit' );
		}

		/**
		 * Store the version used to calculate the discounts.
		 * If the '_store_credit_used' meta doesn't exists. It was created before version 2.2.
		 */
		$order->update_meta_data( '_store_credit_version', '2.1' );

		// Update the credit used meta.
		wc_update_store_credit_used_for_order( $order, $credit );
	}
}

/**
 * Sets the payment method to 'Store credit' to the orders paid with a store credit coupon.
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_store_credit_update_240_set_payment_method_to_orders() {
	$orders = wc_get_orders(
		array(
			'type'               => 'shop_order',
			'status'             => array( 'wc-processing', 'wc-completed' ),
			'limit'              => -1,
			'store_credit_query' => array(
				array(
					'key'   => '_payment_method',
					'value' => '',
				),
				array(
					'key'     => '_order_total',
					'value'   => 0,
					'compare' => '<=',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => '_store_credit_used',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	if ( ! empty( $orders ) ) {
		$payment_method = _x( 'Store Credit', 'payment method', 'woocommerce-store-credit' );

		foreach ( $orders as $order ) {
			$order->update_meta_data( '_payment_method', $payment_method );
			$order->save_meta_data();
		}
	}
}

/**
 * Clears the remaining credit from trashed store credit coupons.
 *
 * The coupons were trashed without decreasing the credit.
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_store_credit_update_240_clear_exhausted_coupons() {
	global $wpdb;

	$coupon_ids = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_coupon',
			'post_status'    => 'trash',
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'discount_type',
					'value' => 'store_credit',
				),
				array(
					'key'     => 'coupon_amount',
					'value'   => 0,
					'compare' => '>',
				),
			),
		)
	);

	if ( ! empty( $coupon_ids ) ) {
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query(
			"UPDATE $wpdb->postmeta as metas
			SET meta_value = 0
			WHERE meta_key = 'coupon_amount' AND
				  metas.post_id IN ('" . implode( "','", $coupon_ids ) . "')"
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}
}

/**
 * Updates DB Version.
 */
function wc_store_credit_update_240_db_version() {
	WC_Store_Credit_Install::update_db_version( '2.4.0' );
}

/**
 * Migrates the plugin settings to the new version.
 */
function wc_store_credit_update_300_migrate_settings() {
	// Rename existing settings.
	$rename_settings = array(
		'woocommerce_store_credit_show_my_account',
		'woocommerce_store_credit_individual_use',
	);

	foreach ( $rename_settings as $setting_name ) {
		$value = get_option( $setting_name );

		// It's defined.
		if ( false !== $value ) {
			add_option( str_replace( 'woocommerce_', 'wc_', $setting_name ), $value );
			delete_option( $setting_name );
		}
	}

	// Update coupon behaviour.
	$before_tax = wc_string_to_bool( get_option( 'woocommerce_store_credit_apply_before_tax', 'no' ) );

	add_option( 'wc_store_credit_inc_tax', wc_bool_to_string( ! $before_tax ) );
	add_option( 'wc_store_credit_apply_to_shipping', wc_bool_to_string( ! $before_tax ) );

	// Delete obsolete settings.
	delete_option( 'woocommerce_store_credit_apply_before_tax' );
	delete_option( 'woocommerce_delete_store_credit_after_usage' );
	delete_option( 'woocommerce_store_credit_coupons_retention' );
}

/**
 * Stores the orders that need to update the version used to calculate the store credit discounts.
 */
function wc_store_credit_update_300_orders_to_update_credit_version() {
	// If the '_store_credit_version' meta doesn't exist, it was created between versions 2.2 and 3.0.
	$order_ids = wc_get_orders(
		array(
			'type'               => 'shop_order',
			'return'             => 'ids',
			'limit'              => -1,
			'store_credit_query' => array(
				array(
					'key'     => '_store_credit_used',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_store_credit_version',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_store_credit_discounts',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	update_option( 'wc_store_credit_update_300_orders_to_update_credit_version', $order_ids );
}

/**
 * Updates the version used to calculate the store credit discounts in older orders.
 *
 * @return bool True to run it again, false if it's completed.
 */
function wc_store_credit_update_300_update_orders_credit_version() {
	return wc_store_credit_update_batch_process(
		'wc_store_credit_update_300_orders_to_update_credit_version',
		'wc_store_credit_update_300_update_order_credit_version'
	);
}

/**
 * Updates the version used to calculate the discounts for the specified order.
 *
 * @param int $order_id Order ID.
 */
function wc_store_credit_update_300_update_order_credit_version( $order_id ) {
	$order = wc_store_credit_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$coupon_items = wc_get_store_credit_coupons_for_order( $order );

	if ( ! empty( $coupon_items ) ) {
		$coupon_item = reset( $coupon_items );

		$order->update_meta_data( '_store_credit_before_tax', wc_bool_to_string( $coupon_item->get_discount_tax( 'edit' ) > 0 ) );
	}

	$order->update_meta_data( '_store_credit_version', '2.2' );
	$order->save();
}

/**
 * Stores the orders that need to update the discounts applied by store credit coupons.
 */
function wc_store_credit_update_300_orders_to_update_credit_discounts() {
	// Orders with store credit coupons applied before taxes and without the metadata '_store_credit_discounts'.
	$order_ids = wc_get_orders(
		array(
			'limit'              => - 1,
			'type'               => 'shop_order',
			'return'             => 'ids',
			'store_credit_query' => array(
				array(
					'key'     => '_store_credit_used',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_store_credit_discounts',
					'compare' => 'NOT EXISTS',
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => '_store_credit_before_tax',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => '_store_credit_before_tax',
						'value' => 'yes',
					),
				),
			),
		)
	);

	update_option( 'wc_store_credit_update_300_orders_to_update_credit_discounts', $order_ids );
}

/**
 * Updates the discounts applied by store credit coupons in older orders.
 *
 * @return bool True to run it again, false if it's completed.
 */
function wc_store_credit_update_300_update_orders_credit_discounts() {
	return wc_store_credit_update_batch_process(
		'wc_store_credit_update_300_orders_to_update_credit_discounts',
		'wc_store_credit_update_300_update_order_credit_discounts'
	);
}

/**
 * Updates the discounts applied by store credit coupons for the specified order.
 *
 * @param int $order_id Order ID.
 */
function wc_store_credit_update_300_update_order_credit_discounts( $order_id ) {
	$order = wc_store_credit_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$coupon_items = wc_get_store_credit_coupons_for_order( $order );

	if ( empty( $coupon_items ) ) {
		return;
	}

	$discounts = array();

	foreach ( $coupon_items as $coupon_item_id => $coupon_item ) {
		$discounts[ $coupon_item->get_code( 'edit' ) ] = array(
			'cart'     => (string) $coupon_item->get_discount( 'edit' ),
			'cart_tax' => (string) $coupon_item->get_discount_tax( 'edit' ),
		);
	}

	$order->update_meta_data( '_store_credit_discounts', $discounts );
	$order->save();
}

/**
 * Stores the coupon that need to be updated.
 */
function wc_store_credit_update_300_coupons_to_update() {
	// Store credit coupons without the metadata 'store_credit_inc_tax'.
	$coupon_ids = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_coupon',
			'post_status'    => array( 'publish', 'trash' ),
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'discount_type',
					'value' => 'store_credit',
				),
				array(
					'key'     => 'store_credit_inc_tax',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	update_option( 'wc_store_credit_update_300_coupons_to_update', $coupon_ids );
}

/**
 * Updates the coupons.
 *
 * Adds the global settings 'inc_tax' and 'apply_to_shipping' as metadata.
 *
 * @return bool True to run it again, false if it's completed.
 */
function wc_store_credit_update_300_update_coupons() {
	return wc_store_credit_update_batch_process(
		'wc_store_credit_update_300_coupons_to_update',
		'wc_store_credit_update_300_update_coupon',
		array(
			'store_credit_inc_tax'           => wc_bool_to_string( get_option( 'wc_store_credit_inc_tax', 'no' ) ),
			'store_credit_apply_to_shipping' => wc_bool_to_string( get_option( 'wc_store_credit_apply_to_shipping', 'no' ) ),
		)
	);
}

/**
 * Updates the metadata of a store credit coupon.
 *
 * @param int   $coupon_id Coupon ID.
 * @param array $metas     The metadata to add.
 */
function wc_store_credit_update_300_update_coupon( $coupon_id, $metas ) {
	$coupon = wc_store_credit_get_coupon( $coupon_id );

	if ( $coupon ) {
		// Fetch the configuration from an order where the coupon was applied.
		$order_ids = wc_store_credit_get_coupon_orders( $coupon );

		if ( ! empty( $order_ids ) ) {
			$order_id   = reset( $order_ids );
			$before_tax = wc_store_credit_apply_before_tax( $order_id );

			$metas['store_credit_inc_tax']           = wc_bool_to_string( ! $before_tax );
			$metas['store_credit_apply_to_shipping'] = wc_bool_to_string( ! $before_tax );
		}

		foreach ( $metas as $key => $value ) {
			// Don't overwrite it if already exists.
			if ( ! $coupon->meta_exists( $key ) ) {
				$coupon->add_meta_data( $key, $value, true );
			}
		}

		$coupon->save();
	}
}

/**
 * Updates DB Version.
 */
function wc_store_credit_update_300_db_version() {
	WC_Store_Credit_Install::update_db_version( '3.0.0' );
}
