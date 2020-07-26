<?php
/**
 * Backward compatibility functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

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

	$order_id = ( $the_order instanceof WC_Order ? $the_order->get_id() : intval( $the_order ) );

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
 * @return bool
 */
function wc_od_update_order_meta( $the_order, $key, $value, $save = false ) {
	$updated = false;

	if ( $the_order instanceof WC_Order ) {
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
		$updated = (bool) update_post_meta( $the_order, $key, $value );
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
 * @return bool
 */
function wc_od_delete_order_meta( $the_order, $key, $save = false ) {
	if ( $the_order instanceof WC_Order ) {
		$the_order->delete_meta_data( $key );
		$deleted = true;

		// Save the meta immediately.
		if ( $save ) {
			$the_order->save_meta_data();
		}
	} else {
		$deleted = delete_post_meta( $the_order, $key );
	}

	return $deleted;
}
