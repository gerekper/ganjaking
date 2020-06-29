<?php
/**
 * Order functions
 *
 * @package WC_Store_Credit/Functions
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the order instance.
 *
 * @since 2.4.0
 * @since 3.0.0 Return false for order refunds.
 *
 * @param mixed $the_order Order object or ID.
 * @return false|WC_Order The WC_Order object. False on failure.
 */
function wc_store_credit_get_order( $the_order ) {
	$order = ( $the_order instanceof WC_Abstract_Order ? $the_order : wc_get_order( $the_order ) );

	// Return false for WC_Order_Refund objects.
	return ( $order instanceof WC_Order ? $order : false );
}

/**
 * Gets an order meta data by key.
 *
 * @since 2.4.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $key       Optional. The meta key to retrieve.
 * @param bool   $single    Optional. Whether to return a single value. Default true.
 * @return mixed The meta data value.
 */
function wc_store_credit_get_order_meta( $the_order, $key = '', $single = true ) {
	$order = wc_store_credit_get_order( $the_order );

	return ( $order ? $order->get_meta( $key, $single ) : '' );
}

/**
 * Gets the discount type for the specified order coupon.
 *
 * An 'order coupon' is a WC_Order_Item object or an array that represents a coupon applied to an order.
 *
 * @since 2.4.0
 *
 * @param mixed               $the_order   Order object or ID.
 * @param WC_Order_Item|array $coupon_item The order coupon.
 * @return mixed|string
 */
function wc_store_credit_get_order_coupon_type( $the_order, $coupon_item ) {
	// The coupon 'type' property doesn't work when the coupon is trashed or removed.
	$type = '';

	if ( $coupon_item instanceof WC_Order_Item ) {
		$coupon_data = $coupon_item->get_meta( 'coupon_data', true );

		if ( is_array( $coupon_data ) && ! empty( $coupon_data['discount_type'] ) ) {
			$type = $coupon_data['discount_type'];
		} else {
			$type = $coupon_item->get_meta( 'discount_type', true );
		}
	} elseif ( is_array( $coupon_item ) ) {
		if ( ! empty( $coupon_item['discount_type'] ) ) {
			$type = $coupon_item['discount_type'];
		} elseif ( ! empty( $coupon_item['id'] ) ) {
			$type = get_post_meta( $coupon_item['id'], 'discount_type', true );
		}
	}

	if ( ! $type && ! empty( $coupon_item['name'] ) ) {
		// Let's try with the coupon object.
		$coupon_id = wc_store_credit_get_coupon_id_by_code( $coupon_item['name'] );

		if ( $coupon_id ) {
			$coupon = wc_store_credit_get_coupon( $coupon_id );
			$type   = $coupon->get_discount_type();
		} else {
			// Try with the credit used in the order.
			$credit_used = wc_get_store_credit_used_for_order( $the_order, 'per_coupon' );

			// It's in the credit used. So, it's a store credit coupon.
			if ( ! empty( $credit_used[ $coupon_item['name'] ] ) ) {
				$type = 'store_credit';
			}
		}
	}

	return $type;
}

/**
 * Gets the Store Credit coupons applied to the specified order.
 *
 * @since 2.4.0
 *
 * @param mixed $the_order Order object or ID.
 * @return array|false An array with the order coupons. False on failure.
 */
function wc_get_store_credit_coupons_for_order( $the_order ) {
	$order = wc_store_credit_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	$coupons      = array();
	$coupon_items = $order->get_items( 'coupon' );

	if ( ! empty( $coupon_items ) ) {
		foreach ( $coupon_items as $coupon_item_id => $coupon_item ) {
			if ( 'store_credit' === wc_store_credit_get_order_coupon_type( $order, $coupon_item ) ) {
				$coupons[ $coupon_item_id ] = $coupon_item;
			}
		}
	}

	return $coupons;
}

/**
 * Gets the version used to calculate the discounts of the Store Credit coupons to the specified order.
 *
 * @since 2.4.0
 *
 * @param mixed $the_order Order object or ID.
 * @return string
 */
function wc_get_store_credit_version_for_order( $the_order ) {
	$version = wc_store_credit_get_order_meta( $the_order, '_store_credit_version' );

	return ( $version ? $version : '3.0' );
}

/**
 * Gets the store credit used in the specified order.
 *
 * @since 2.3.0
 * @since 2.4.0 Added `$return` parameter.
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $return    Optional. The way to return the data. Accepts 'total', 'per_coupon'. Default 'total'.
 * @return mixed The total credit used or an array with pairs [coupon_code => credit].
 */
function wc_get_store_credit_used_for_order( $the_order, $return = 'total' ) {
	$credit = wc_store_credit_get_order_meta( $the_order, '_store_credit_used' );

	if ( ! is_array( $credit ) ) {
		$credit = array();
	}

	if ( 'total' === $return ) {
		$credit = array_sum( $credit );
	}

	return $credit;
}

/**
 * Updates the store credit used in the specified order.
 *
 * Automatically deletes the credit used if it's empty or `$value` is zero.
 *
 * @since 2.4.0
 * @since 3.0.0 Added a fourth parameter for saving the order.
 *
 * @param mixed $the_order Order object or ID.
 * @param mixed $code      The coupon code or an array in pairs [coupon_code => credit].
 * @param mixed $value     Optional. The credit used by the specified coupon code. Only when `$code` is a coupon code. Default null.
 * @param bool  $save      Optional. Save the order after updating the credit used. Default true.
 * @return bool True if the credit was updated. False otherwise.
 */
function wc_update_store_credit_used_for_order( $the_order, $code, $value = null, $save = true ) {
	$order = wc_store_credit_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	if ( is_array( $code ) ) {
		$credit_used = $code;
	} else {
		$credit_used     = wc_get_store_credit_used_for_order( $order, 'per_coupon' );
		$credit[ $code ] = $value;
	}

	// Remove falsy values, including coupons with zero discount.
	$credit_used = array_filter( $credit_used );

	// Update credit used.
	if ( ! empty( $credit_used ) ) {
		// Convert the amounts to strings to avoid invalid rounded float values.
		$credit_used = array_map( 'strval', $credit_used );

		$order->update_meta_data( '_store_credit_used', $credit_used );
	} else {
		$order->delete_meta_data( '_store_credit_used' );
		$order->delete_meta_data( '_store_credit_version' );
		$order->delete_meta_data( '_store_credit_before_tax' );
		$order->delete_meta_data( '_store_credit_discounts' );
	}

	if ( $save ) {
		$order->save();
	}

	return true;
}

/**
 * Gets the discounts applied by store credit coupons to the specified order.
 *
 * @since 3.0.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param string $return    Optional. The way to return the data. Accepts 'total', 'per_coupon'. Default 'total'.
 * @param array  $keys      Optional. Filter the discounts to retrieve. Default empty.
 * @return array An array with the discounts grouped by type or coupon.
 */
function wc_get_store_credit_discounts_for_order( $the_order, $return = 'total', $keys = array() ) {
	$discounts = wc_store_credit_get_order_meta( $the_order, '_store_credit_discounts' );

	if ( ! is_array( $discounts ) ) {
		$discounts = array();
	}

	// Filter the discounts to retrieve.
	if ( ! empty( $keys ) ) {
		foreach ( $discounts as $coupon_code => $coupon_discounts ) {
			$discounts[ $coupon_code ] = array_intersect_key( $coupon_discounts, array_flip( $keys ) );
		}
	}

	// Combine discounts by type.
	if ( 'total' === $return ) {
		$discounts = wc_store_credit_combine_amounts( $discounts );
	}

	return $discounts;
}

/**
 * Gets the store credit for the specified order.
 *
 * @since 3.0.0
 *
 * @param mixed  $the_order Order object or ID.
 * @param bool   $inc_tax   Optional. Include taxes. Default false.
 * @param string $only      Optional. Filter the discounts to retrieve. Accepts empty, 'cart', 'shipping'. Default empty.
 * @return float
 */
function wc_get_store_credit_for_order( $the_order, $inc_tax = false, $only = '' ) {
	$order = wc_store_credit_get_order( $the_order );

	if ( wc_store_credit_apply_before_tax( $order ) ) {
		$type = ( $inc_tax ? 'base_tax' : 'base' );
		$keys = wc_store_credit_discount_type_keys( $type );

		if ( $only ) {
			foreach ( $keys as $index => $key ) {
				if ( ! wc_store_credit_starts_with( $key, $only ) ) {
					unset( $keys[ $index ] );
				}
			}

			$keys = array_values( $keys );
		}

		$credit = array_sum( wc_get_store_credit_discounts_for_order( $order, 'total', $keys ) );
	} else {
		// We cannot give the credit without taxes for this case.
		$credit = wc_get_store_credit_used_for_order( $order );
	}

	return $credit;
}

/**
 * Gets the store credit (formatted) for the specified order.
 *
 * @since 2.4.6
 *
 * @param mixed  $the_order   Order object or ID.
 * @param string $tax_display Optional. Excl or incl tax display mode.
 * @return string
 */
function wc_get_store_credit_to_display_for_order( $the_order, $tax_display = '' ) {
	$order  = wc_store_credit_get_order( $the_order );
	$credit = wc_get_store_credit_for_order( $order, ( 'incl' === $tax_display ) );

	if ( $credit > 0 ) {
		$tax_label  = '';
		$inc_tax    = $order->get_prices_include_tax();
		$before_tax = wc_store_credit_apply_before_tax( $order );

		if ( 'excl' === $tax_display && $inc_tax && (float) $before_tax ) {
			$tax_label = WC()->countries->ex_tax_or_vat();
		} elseif ( 'incl' === $tax_display && ! $inc_tax && (float) $before_tax ) {
			$tax_label = WC()->countries->inc_tax_or_vat();
		}

		if ( $tax_label ) {
			$tax_label = '&nbsp;<small class="tax_label">' . $tax_label . '</small>';

			/**
			 * Filters the tax label for the formatted store credit of the specified order.
			 *
			 * @since 2.4.6
			 *
			 * @param string   $tax_label   The formatted tax label.
			 * @param WC_Order $order       Order object.
			 * @param string   $tax_display Excl or incl tax display mode.
			 */
			$tax_label = apply_filters( 'wc_get_order_store_credit_to_display_tax_label', $tax_label, $order, $tax_display );
		}

		$credit = wc_price( $credit, array( 'currency' => $order->get_currency() ) ) . $tax_label;
	}

	/**
	 * Filters the formatted store credit for the specified order.
	 *
	 * @since 2.4.6
	 *
	 * @param string   $credit      The formatted store credit.
	 * @param WC_Order $order       Order object.
	 * @param string   $tax_display Excl or incl tax display mode.
	 */
	return apply_filters( 'wc_get_order_store_credit_to_display', $credit, $order, $tax_display );
}

/**
 * Restores the credit used by the order.
 *
 * @since 2.4.0
 *
 * @param mixed $the_order Order object or ID.
 */
function wc_restore_store_credit_for_order( $the_order ) {
	$order = wc_store_credit_get_order( $the_order );

	// Order not found.
	if ( ! $order ) {
		return;
	}

	$order_cancelled = ( 'cancelled' === $order->get_status() );
	$coupon_items    = wc_get_store_credit_coupons_for_order( $order );
	$credit_used     = wc_get_store_credit_used_for_order( $order, 'per_coupon' );

	foreach ( $coupon_items as $coupon_item_id => $coupon_item ) {
		// Coupon restored previously.
		if ( wc_string_to_bool( $coupon_item->get_meta( 'restored', true, 'edit' ) ) ) {
			continue;
		}

		try {
			// Set the coupon as 'restored'.
			wc_add_order_item_meta( $coupon_item_id, 'restored', 'yes' );
		} catch ( Exception $e ) {
			continue;
		}

		$coupon_code = $coupon_item->get_code();
		$coupon      = wc_store_credit_get_coupon( $coupon_code );

		// Coupon not found.
		if ( ! $coupon ) {
			continue;
		}

		// WC decreases the 'usage count' for cancelled orders automatically.
		if ( ! $order_cancelled ) {
			$coupon->decrease_usage_count();
		}

		$discount = ( isset( $credit_used[ $coupon_code ] ) ? $credit_used[ $coupon_code ] : 0 );

		if ( $discount ) {
			wc_update_store_credit_coupon_balance( $coupon, $discount, 'increase' );
		}
	}
}

/**
 * Deletes the restored store credit coupons used in the order.
 *
 * @since 2.4.0
 * @since 3.0.0 Added parameter `$from_status`.
 *
 * @param mixed  $the_order   Order object or ID.
 * @param string $from_status Optional. Previous order status.
 */
function wc_store_credit_delete_restored_coupons_for_order( $the_order, $from_status = '' ) {
	$order = wc_store_credit_get_order( $the_order );

	// Order not found.
	if ( ! $order ) {
		return;
	}

	$coupon_items = wc_get_store_credit_coupons_for_order( $order );
	$credit_used  = wc_get_store_credit_used_for_order( $order, 'per_coupon' );

	/*
	 * Remove the non-restored coupons from the list.
	 * Also, remove the restored coupons from the credit used to not increase its credit.
	 */
	foreach ( $coupon_items as $coupon_item_id => $coupon_item ) {
		if ( wc_string_to_bool( $coupon_item->get_meta( 'restored', true, 'edit' ) ) ) {
			unset( $credit_used[ $coupon_item->get_code() ] );
		} else {
			unset( $coupon_item[ $coupon_item_id ] );
		}
	}

	// Silently update the store credit data before removing the coupons.
	if ( empty( $credit_used ) ) {
		$order->delete_meta_data( '_store_credit_used' );
		$order->delete_meta_data( '_store_credit_version' );
		$order->delete_meta_data( '_store_credit_before_tax' );
		$order->delete_meta_data( '_store_credit_discounts' );
	} else {
		$order->update_meta_data( '_store_credit_used', $credit_used );
	}

	$order->save();

	if ( empty( $coupon_items ) ) {
		return;
	}

	foreach ( $coupon_items as $coupon_item_id => $coupon_item ) {
		$coupon_code = $coupon_item->get_code();

		/*
		 * The coupon 'usage' count is decreased on removing.
		 * But we already decreased the counter after marking the coupon as 'restored'.
		 * So, we increase the counter once to keep it unaltered when removing the coupon from the order.
		 * Not applicable if coming from a cancelled order.
		 */
		if ( 'cancelled' !== $from_status ) {
			$coupon = wc_store_credit_get_coupon( $coupon_code );

			if ( $coupon ) {
				$coupon->increase_usage_count();
				$coupon->save();
			}
		}

		$order->remove_coupon( $coupon_code );
	}
}

/**
 * Deletes the exhausted store credit coupons in the specified order.
 *
 * @since 2.4.0
 *
 * @param mixed $the_order Order object or ID.
 */
function wc_store_credit_delete_exhausted_order_coupons( $the_order ) {
	$credit = wc_get_store_credit_used_for_order( $the_order, 'per_coupon' );

	array_map( 'wc_store_credit_maybe_delete_coupon', array_keys( $credit ) );
}

/**
 * Creates a Store Credit coupon from an order item.
 *
 * @since 3.2.0
 *
 * @param WC_Order_Item_Product $order_item The order item product.
 */
function wc_store_credit_create_coupon_from_order_item( $order_item ) {
	$data = $order_item->get_meta( '_store_credit_data' );

	if ( ! $data || $order_item->get_meta( '_store_credit_coupons' ) ) {
		return;
	}

	$product = $order_item->get_product();

	if ( ! $product || ! $product->is_type( 'store_credit' ) ) {
		return;
	}

	$order    = $order_item->get_order();
	$amount   = ( ! empty( $data['amount'] ) ? $data['amount'] : $product->get_regular_price() );
	$receiver = $order_item->get_meta( '_store_credit_receiver' );

	if ( ! $receiver ) {
		$receiver = array();
	}

	$customer_email = ( ! empty( $receiver['email'] ) ? $receiver['email'] : $order->get_billing_email() );

	if ( ! empty( $receiver['note'] ) ) {
		$data['description'] = $receiver['note'];
	}

	unset( $data['amount'] );

	$coupon_codes = array();
	$coupon_qty   = $order_item->get_quantity();

	for ( $i = 0; $i < $coupon_qty; $i++ ) {
		$coupon = wc_store_credit_send_credit_to_customer( $customer_email, $amount, $data );

		if ( $coupon ) {
			$coupon_codes[] = $coupon->get_code();
		}
	}

	if ( ! empty( $coupon_codes ) ) {
		$order_item->add_meta_data( '_store_credit_coupons', $coupon_codes );
		$order_item->save_meta_data();
	}
}

/**
 * Gets the order item meta by key.
 *
 * It returns the WC_Meta_Data object, not the meta value.
 *
 * @since 3.2.0
 *
 * @param WC_Order_Item $order_item Order item object.
 * @param string        $meta_key   Order item meta key.
 * @return WC_Meta_Data|false The meta object. False on failure.
 */
function wc_store_credit_get_order_item_meta( $order_item, $meta_key ) {
	$metas      = $order_item->get_meta_data();
	$array_keys = wp_list_pluck( $metas, 'key' );
	$index      = array_search( $meta_key, $array_keys, true );

	return ( false !== $index && isset( $metas[ $index ] ) ? $metas[ $index ] : false );
}
