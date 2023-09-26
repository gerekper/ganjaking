<?php
/**
 * Backward compatibility functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets an order meta by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       Optional. The meta key to retrieve.
 * @param bool   $single    Optional. Whether to return a single value. Default true.
 * @return mixed The meta data value.
 */
function wc_od_get_order_meta( $the_order, $key = '', $single = true ) {
	$order = wc_od_get_order( $the_order );

	return ( $order ? $order->get_meta( $key, $single ) : '' );
}

/**
 * Updates an order meta by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       The meta key to update.
 * @param mixed  $value     The meta value.
 * @param bool   $save      Optional. True to save the meta immediately. Default false.
 * @return bool
 */
function wc_od_update_order_meta( $the_order, $key, $value, $save = false ) {
	$order = wc_od_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	$updated   = false;
	$old_value = $order->get_meta( $key );

	if ( $old_value !== $value ) {
		$order->update_meta_data( $key, $value );
		$updated = true;

		// Save the meta immediately.
		if ( $save ) {
			$order->save_meta_data();
		}
	}

	return $updated;
}

/**
 * Deletes an order meta by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       The meta key to delete.
 * @param bool   $save      Optional. True to delete the meta immediately. Default false.
 * @return bool
 */
function wc_od_delete_order_meta( $the_order, $key, $save = false ) {
	$order = wc_od_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	$order->delete_meta_data( $key );

	// Save the meta immediately.
	if ( $save ) {
		$order->save_meta_data();
	}

	return true;
}

/**
 * Gets whether the custom order tables are enabled or not.
 *
 * @since 2.4.0
 *
 * @return bool
 */
function wc_od_is_custom_order_tables_enabled() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
		return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}

	return false;
}

/**
 * Gets the screen name of orders page in wp-admin.
 *
 * @since 2.4.0
 *
 * @return string
 */
function wc_od_get_order_admin_screen() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
		return \Automattic\WooCommerce\Utilities\OrderUtil::get_order_admin_screen();
	}

	return 'shop_order';
}

/**
 * Gets value of a meta key from WC_Data object if passed, otherwise from the post object.
 *
 * @since 2.4.0
 *
 * @param WP_Post|null $post   Post object, meta will be fetched from this only when `$data` is not passed.
 * @param WC_Data|null $data   WC_Data object, will be preferred over post object when passed.
 * @param string       $key    Key to fetch metadata for.
 * @param bool         $single Whether metadata is single.
 * @return array|mixed|string Value of the meta key.
 */
function wc_od_get_post_or_object_meta( $post, $data, $key, $single ) {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
		return \Automattic\WooCommerce\Utilities\OrderUtil::get_post_or_object_meta( $post, $data, $key, $single );
	}

	return get_post_meta( $post->ID, $key, $single );
}

/**
 * Gets the ID of the currently editing post or object.
 *
 * @since 2.6.0
 *
 * @param string $screen Optional. Check the current screen matches the provided. Default empty.
 * @return int|false The object ID. False otherwise.
 */
function wc_od_get_current_post_or_object_id( $screen = '' ) {
	if ( $screen && wc_od_get_current_screen_id() !== $screen ) {
		return false;
	}

	$object_id = false;

	// phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $_GET['id'] ) ) {
		$object_id = absint( wp_unslash( $_GET['id'] ) );
	} elseif ( isset( $_GET['post'] ) ) {
		$object_id = absint( wp_unslash( $_GET['post'] ) );
	}
	// phpcs:enable WordPress.Security.NonceVerification

	return $object_id;
}
