<?php
/**
 * Coupon functions
 *
 * @package WC_Store_Credit/Functions
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the coupon instance.
 *
 * @since 2.4.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return WC_Coupon|false The WC_Coupon object. False on failure.
 */
function wc_store_credit_get_coupon( $the_coupon ) {
	if ( $the_coupon instanceof WC_Coupon ) {
		return $the_coupon;
	}

	$coupon_id = wc_store_credit_get_coupon_id( $the_coupon );

	return ( $coupon_id ? new WC_Coupon( $coupon_id ) : false );
}

/**
 * Gets the coupon ID by code.
 *
 * @since 2.4.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 *
 * @param string $code Coupon code.
 * @return int|false The coupon ID. False otherwise.
 */
function wc_store_credit_get_coupon_id_by_code( $code ) {
	global $wpdb;

	$cache_key = 'wc_store_credit_coupon_id_from_code_' . $code;
	$ids       = wp_cache_get( $cache_key, 'coupons' );

	if ( false === $ids ) {
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID
				 FROM $wpdb->posts
				 WHERE post_title = %s AND post_type = 'shop_coupon'
				 ORDER BY post_date DESC",
				$code
			)
		);

		if ( $ids ) {
			wp_cache_set( $cache_key, $ids, 'coupons' );
		}
	}

	$ids = array_filter( array_map( 'absint', (array) $ids ) );

	/**
	 * Filters the ID of the coupon fetched by code.
	 *
	 * @since 2.4.0
	 *
	 * @param int    $coupon_id   The coupon ID.
	 * @param string $coupon_code The coupon code.
	 */
	return apply_filters( 'wc_store_credit_get_coupon_id_by_code', current( $ids ), $code );
}

/**
 * Gets the coupon ID.
 *
 * @since 2.4.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return int|false The coupon ID. False otherwise.
 */
function wc_store_credit_get_coupon_id( $the_coupon ) {
	$coupon_id = false;

	if ( $the_coupon instanceof WC_Coupon ) {
		$coupon_id = $the_coupon->get_id();

		// Trashed coupon.
		if ( ! $coupon_id ) {
			$code      = wc_store_credit_get_coupon_code( $the_coupon );
			$coupon_id = wc_store_credit_get_coupon_id_by_code( $code );
		}
	} elseif ( is_numeric( $the_coupon ) ) {
		$coupon_id = $the_coupon;
	} elseif ( is_string( $the_coupon ) ) {
		$coupon_id = wc_store_credit_get_coupon_id_by_code( $the_coupon );
	}

	return ( $coupon_id ? absint( $coupon_id ) : false );
}

/**
 * Gets the coupon code by ID.
 *
 * @since 2.4.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 *
 * @param int $coupon_id Coupon ID.
 * @return string|false The coupon code. False otherwise.
 */
function wc_store_credit_get_coupon_code_by_id( $coupon_id ) {
	global $wpdb;

	$cache_key = 'wc_store_credit_coupon_code_from_id_' . $coupon_id;
	$code      = wp_cache_get( $cache_key, 'coupons' );

	if ( false === $code ) {
		$code = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_title
				 FROM $wpdb->posts
				 WHERE ID = %d AND post_type = 'shop_coupon'",
				$coupon_id
			)
		);

		if ( $code ) {
			wp_cache_set( $cache_key, $code, 'coupons' );
		}
	}

	/**
	 * Filters the code of the coupon fetched by ID.
	 *
	 * @since 2.4.0
	 *
	 * @param string $coupon_code The coupon code.
	 * @param int    $coupon_id   The coupon ID.
	 */
	return apply_filters( 'wc_store_credit_get_coupon_code_by_id', $code, $coupon_id );
}

/**
 * Gets the coupon code.
 *
 * @since 2.4.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return string|false The coupon code. False otherwise.
 */
function wc_store_credit_get_coupon_code( $the_coupon ) {
	$coupon_code = false;

	if ( $the_coupon instanceof WC_Coupon ) {
		$coupon_code = $the_coupon->get_code();
	} elseif ( is_numeric( $the_coupon ) ) {
		$coupon_code = wc_store_credit_get_coupon_code_by_id( $the_coupon );
	} elseif ( is_string( $the_coupon ) ) {
		$coupon_code = $the_coupon;
	}

	return ( $coupon_code ? $coupon_code : false );
}

/**
 * Gets if it's a 'store_credit' coupon or not.
 *
 * @since 2.2.0
 * @since 2.4.0 Also accepts a coupon code or the coupon ID as parameter.
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return bool
 */
function wc_is_store_credit_coupon( $the_coupon ) {
	$coupon    = wc_store_credit_get_coupon( $the_coupon );
	$is_coupon = ( $coupon && $coupon->is_type( 'store_credit' ) );

	/**
	 * Filters if it's a 'store_credit' coupon or not.
	 *
	 * @since 2.4.0
	 *
	 * @param bool  $is_coupon  True if it's a 'store_credit' coupon. False otherwise.
	 * @param mixed $the_coupon Coupon object, ID or code.
	 */
	return apply_filters( 'wc_is_store_credit_coupon', $is_coupon, $the_coupon );
}

/**
 * Gets if the coupon amount includes taxes.
 *
 * @since 3.0.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return bool
 */
function wc_store_credit_coupon_include_tax( $the_coupon ) {
	$coupon  = wc_store_credit_get_coupon( $the_coupon );
	$inc_tax = false;

	if ( $coupon ) {
		$inc_tax_meta = $coupon->get_meta( 'store_credit_inc_tax' );

		// Use the global option if the coupon meta doesn't exist.
		$inc_tax = (
			'' !== $inc_tax_meta ?
			wc_string_to_bool( $inc_tax_meta ) :
			wc_store_credit_coupons_can_inc_tax() && wc_string_to_bool( get_option( 'wc_store_credit_inc_tax', 'no' ) )
		);
	}

	/**
	 * Filters if the coupon amount includes taxes.
	 *
	 * @since 3.0.0
	 *
	 * @param bool      $inc_tax True if the coupon amount includes taxes. False otherwise.
	 * @param WC_Coupon $coupon  Coupon object.
	 */
	return apply_filters( 'wc_store_credit_coupon_include_tax', $inc_tax, $coupon );
}

/**
 * Gets if the coupon can be applied to the shipping costs.
 *
 * @since 3.0.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return bool
 */
function wc_store_credit_coupon_apply_to_shipping( $the_coupon ) {
	$coupon            = wc_store_credit_get_coupon( $the_coupon );
	$apply_to_shipping = false;

	if ( $coupon && ! $coupon->get_free_shipping() ) {
		$shipping_meta = $coupon->get_meta( 'store_credit_apply_to_shipping' );

		$apply_to_shipping = (
			'' !== $shipping_meta ?
			wc_string_to_bool( $shipping_meta ) :
			wc_shipping_enabled() && wc_string_to_bool( get_option( 'wc_store_credit_apply_to_shipping', 'no' ) )
		);
	}

	/**
	 * Filters if the coupon can be applied to the shipping costs.
	 *
	 * @since 3.0.0
	 *
	 * @param bool      $apply_to_shipping True to apply the coupon to the shipping costs. False otherwise.
	 * @param WC_Coupon $coupon            Coupon object.
	 */
	return apply_filters( 'wc_store_credit_coupon_apply_to_shipping', $apply_to_shipping, $coupon );
}

/**
 * Updates the coupon balance.
 *
 * Increase or decrease the coupon credit with the specified amount.
 *
 * @since 2.4.0
 *
 * @param mixed  $the_coupon Coupon object, ID or code.
 * @param float  $amount     The amount to modify.
 * @param string $action     Optional. The update type ['decrease', 'increase']. Default 'decrease'.
 */
function wc_update_store_credit_coupon_balance( $the_coupon, $amount, $action = 'decrease' ) {
	$coupon = wc_store_credit_get_coupon( $the_coupon );

	if ( ! $amount || ! $coupon || ! wc_is_store_credit_coupon( $coupon ) ) {
		return;
	}

	$coupon_id = $coupon->get_id();
	$balance   = $coupon->get_amount();

	// Sanitize the amount.
	$amount = abs( $amount );

	if ( 'increase' === $action ) {
		$balance += $amount;
	} else {
		$balance -= $amount;

		if ( $balance < 0 ) {
			$balance = 0;
		}
	}

	// Maybe restore the coupon from the trash.
	if ( 0 < $balance ) {
		wp_untrash_post( $coupon_id );
	}

	$coupon->set_amount( $balance );
	$coupon->save();
}

/**
 * Gets the orders where the coupon was used.
 *
 * Use `$args` to filter the query or return the order objects instead of the IDs ( 'return' => 'objects' ).
 *
 * @since 2.4.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @param array $args       Optional. Additional arguments for the query.
 * @return array An array with the order IDs or objects.
 */
function wc_store_credit_get_coupon_orders( $the_coupon, $args = array() ) {
	global $wpdb;

	$code = wc_store_credit_get_coupon_code( $the_coupon );

	$order_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_type = 'coupon' AND order_item_name = %s",
			$code
		)
	);

	// The IDs by default.
	$orders = array_map( 'intval', $order_ids );

	// Use the arguments to filter the query or return the order objects instead of the IDs.
	if ( ! empty( $orders ) && ! empty( $args ) ) {
		$args = wp_parse_args(
			$args,
			array(
				'status'   => array_keys( wc_get_order_statuses() ),
				'limit'    => -1,
				'return'   => 'ids',
				'post__in' => $orders,
			)
		);

		$orders = wc_get_orders( $args );
	}

	return $orders;
}

/**
 * Gets if the specified coupon is deletable or not.
 *
 * @since 2.4.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return bool
 */
function wc_store_credit_coupon_is_deletable( $the_coupon ) {
	$coupon = wc_store_credit_get_coupon( $the_coupon );

	// Already deleted.
	if ( ! $coupon ) {
		return false;
	}

	// Don't delete the coupon if it still has credit.
	$deletable = ( 0 >= $coupon->get_amount() );

	if ( $deletable ) {
		// Don't delete the coupon if it's still being used in some orders.
		$orders = wc_store_credit_get_coupon_orders(
			$coupon,
			array(
				'status' => array(
					'wc-pending',
					'wc-processing',
					'wc-on-hold',
				),
			)
		);

		$deletable = empty( $orders );
	}

	/**
	 * Filters if the specified coupon is deletable or not.
	 *
	 * @since 2.4.0
	 *
	 * @param bool      $deletable Is the coupon deletable?
	 * @param WC_Coupon $coupon    The coupon object.
	 */
	return apply_filters( 'wc_store_credit_coupon_is_deletable', $deletable, $coupon );
}

/**
 * Deletes the coupon if it's deletable.
 *
 * @see wc_store_credit_coupon_is_deletable
 *
 * @since 2.4.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 */
function wc_store_credit_maybe_delete_coupon( $the_coupon ) {
	$coupon = wc_store_credit_get_coupon( $the_coupon );

	if ( $coupon && wc_store_credit_coupon_is_deletable( $coupon ) ) {
		wp_trash_post( $coupon->get_id() );
	}
}

/**
 * Generates a coupon code.
 *
 * @since 3.0.0
 *
 * @param array $args {
 *     Optional. An array of arguments.
 *
 *     @type int    $length The length for the coupon code excluding the prefix and suffix. Default '16'.
 *     @type string $prefix The coupon code prefix. Default empty.
 *     @type string $suffix The coupon code suffix. Default empty.
 * }
 * @return mixed The coupon code.
 */
function wc_store_credit_generate_coupon_code( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'length' => 16,
			'format' => '',
		)
	);

	// Format fallback.
	if ( ! $args['format'] ) {
		$args['format'] = get_option( 'wc_store_credit_code_format', '{coupon_code}' );
	}

	// Append the placeholder at the end of the format string if it's not found.
	if ( false === strpos( $args['format'], '{coupon_code}' ) ) {
		$args['format'] .= '{coupon_code}';
	}

	/**
	 * Filters the arguments used to generate the code for a store credit coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args The arguments.
	 */
	$args = apply_filters( 'wc_store_credit_coupon_code_args', $args );

	$code = wp_generate_password( $args['length'], false, false );

	$coupon_code = str_replace( '{coupon_code}', $code, $args['format'] );

	/**
	 * Filters the generated coupon code.
	 *
	 * @since 3.0.0
	 *
	 * @param string $coupon_code The coupon code.
	 * @param array  $args        Optional. Additional arguments.
	 */
	return apply_filters( 'wc_store_credit_coupon_code', $coupon_code, $args );
}

/**
 * Creates a store credit coupon.
 *
 * @since 3.0.0
 * @since 3.0.2 Return false if the coupon is not stored in the database.
 * @since 3.2.0 Added parameter `expiration` to `$args`.
 *
 * @param float $amount    The coupon amount.
 * @param array $args      Optional. Additional coupon arguments.
 * @param array $code_args Optional. The arguments used for generating the code.
 * @return WC_Coupon|false The created coupon. False on failure.
 */
function wc_store_credit_create_coupon( $amount, $args = array(), $code_args = array() ) {
	$coupon_code = wc_store_credit_generate_coupon_code( $code_args );

	$args = wp_parse_args(
		$args,
		array(
			'code'              => $coupon_code,
			'discount_type'     => 'store_credit',
			'amount'            => wc_format_decimal( $amount ),
			'individual_use'    => get_option( 'wc_store_credit_individual_use', 'no' ),
			'inc_tax'           => ( wc_store_credit_coupons_can_inc_tax() && wc_string_to_bool( get_option( 'wc_store_credit_inc_tax', 'no' ) ) ),
			'apply_to_shipping' => ( wc_shipping_enabled() && wc_string_to_bool( get_option( 'wc_store_credit_apply_to_shipping', 'no' ) ) ),
			'metas'             => array(),
		)
	);

	/**
	 * Filters the arguments used to create a store credit coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args The coupon arguments.
	 */
	$args = apply_filters( 'wc_store_credit_coupon_args', $args );

	// Parse the arguments.
	$bool_props = array( 'individual_use', 'exclude_sale_items' );

	foreach ( $bool_props as $bool_prop ) {
		if ( isset( $args[ $bool_prop ] ) ) {
			$args[ $bool_prop ] = wc_string_to_bool( $args[ $bool_prop ] );
		}
	}

	// Set expiration date.
	if ( isset( $args['expiration'] ) ) {
		if ( is_array( $args['expiration'] ) && ! empty( $args['expiration']['number'] ) ) {
			$unit = ( isset( $args['expiration']['unit'] ) ? $args['expiration']['unit'] : 'day' );
			$unit = ( isset( $args['expiration']['period'] ) ? $args['expiration']['period'] : $unit );

			$args['date_expires'] = wc_string_to_timestamp( "+ {$args['expiration']['number']} {$unit}" );
		}

		if ( $args['expiration'] instanceof DateTime ) {
			$args['date_expires'] = $args['expiration']->getTimestamp();
		} elseif ( is_string( $args['expiration'] ) ) {
			$args['date_expires'] = wc_string_to_timestamp( $args['expiration'] );
		}

		unset( $args['expiration'] );
	}

	// Pass the coupon code in the constructor for adding compatibility with other extensions.
	$coupon = new WC_Coupon( $args['code'] );
	$props  = array_diff_key( $args, array_flip( array( 'inc_tax', 'apply_to_shipping', 'metas', 'code' ) ) );

	$coupon->set_props( $props );

	$metas                                   = $args['metas'];
	$metas['store_credit_amount']            = $args['amount'];
	$metas['store_credit_inc_tax']           = wc_bool_to_string( $args['inc_tax'] );
	$metas['store_credit_apply_to_shipping'] = wc_bool_to_string( $args['apply_to_shipping'] );

	foreach ( $metas as $key => $value ) {
		$coupon->add_meta_data( $key, $value, true );
	}

	// Coupon not saved properly.
	if ( ! $coupon->save() ) {
		return false;
	}

	/**
	 * Filters the created coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon The coupon object.
	 * @param array     $args   Optional. Additional arguments.
	 */
	return apply_filters( 'wc_store_credit_coupon', $coupon, $args );
}

/**
 * Creates a 'Store Credit' coupon for the specified customer.
 *
 * @since 3.0.0
 * @since 3.0.1 The parameter `$the_customer` accepts an email.
 *
 * @param mixed $the_customer Customer object, email or ID.
 * @param float $amount       The amount the coupon is worth.
 * @param array $args         Optional. Additional arguments.
 * @param array $code_args    Optional. The arguments used for generating the code.
 * @return WC_Coupon|false The created coupon. False on failure.
 */
function wc_store_credit_create_coupon_for_customer( $the_customer, $amount, $args = array(), $code_args = array() ) {
	$customer_email = wc_store_credit_get_customer_email( $the_customer );

	if ( ! $customer_email ) {
		return false;
	}

	$args['email_restrictions'] = array( $customer_email );

	return wc_store_credit_create_coupon( $amount, $args, $code_args );
}

/**
 * Creates and sends a 'Store Credit' coupon to the specified customer.
 *
 * @since 3.0.0
 * @since 3.0.1 The parameter `$the_customer` accepts an email.
 *
 * @param mixed $the_customer Customer object, email or ID.
 * @param float $amount       The amount the coupon is worth.
 * @param array $args         Optional. Additional arguments.
 * @param array $code_args    Optional. The arguments used for generating the code.
 * @return WC_Coupon|false The coupon object. False on failure.
 */
function wc_store_credit_send_credit_to_customer( $the_customer, $amount, $args = array(), $code_args = array() ) {
	$coupon = wc_store_credit_create_coupon_for_customer( $the_customer, $amount, $args, $code_args );

	if ( $coupon ) {
		/**
		 * Fires the action for sending the Store Credit coupon to the specified customer.
		 *
		 * @since 3.0.0
		 * @since 3.0.1 The parameter `$the_customer` accepts an email.
		 *
		 * @param WC_Coupon $coupon       The coupon object.
		 * @param mixed     $the_customer Customer object, email or ID.
		 * @param array     $args         Optional. Additional arguments.
		 */
		do_action( 'wc_store_credit_send_credit_to_customer', $coupon, $the_customer, $args, $code_args );
	}

	return $coupon;
}

/**
 * Detects when a coupon is going to be deleted or trashed.
 *
 * The coupon data is still accessible at this point.
 *
 * @since 3.1.2
 *
 * @param bool|null $delete Whether to go forward with deletion.
 * @param WP_Post   $post   Post object.
 * @return bool
 */
function wc_store_credit_is_deleting_coupon( $delete, $post ) {
	if ( 'shop_coupon' === $post->post_type ) {
		$filter = current_filter();

		$coupon = wc_store_credit_get_coupon( $post->ID );

		if ( false !== strpos( $filter, 'trash' ) ) {
			/**
			 * Fires before trashing a coupon.
			 *
			 * @since 3.1.2
			 *
			 * @param WC_Coupon $coupon Coupon object.
			 */
			do_action( 'wc_store_credit_before_trash_coupon', $coupon );
		} else {
			/**
			 * Fires before deleting a coupon.
			 *
			 * @since 3.1.2
			 *
			 * @param WC_Coupon $coupon Coupon object.
			 */
			do_action( 'wc_store_credit_before_delete_coupon', $coupon );
		}
	}

	return $delete;
}
add_filter( 'pre_delete_post', 'wc_store_credit_is_deleting_coupon', 10, 2 );
add_filter( 'pre_trash_post', 'wc_store_credit_is_deleting_coupon', 10, 2 );

/**
 * Get the URL to redeem the store credit coupon.
 *
 * @since 3.7.0
 *
 * @param mixed $the_coupon Coupon object, ID or code.
 * @return string|false The URL to redeem the credit. False on failure.
 */
function wc_store_credit_get_redeem_url( $the_coupon ) {
	$coupon = wc_store_credit_get_coupon( $the_coupon );

	if ( ! $coupon || ! wc_is_store_credit_coupon( $coupon ) ) {
		return false;
	}

	/**
	 * Filters the URL to redeem the store credit coupon.
	 *
	 * @since 3.7.0
	 *
	 * @param string    $redeem_url The redeem URL.
	 * @param WC_Coupon $coupon     Coupon object.
	 */
	$redeem_url = apply_filters( 'wc_store_credit_redeem_url', site_url(), $coupon );

	return add_query_arg( array( 'redeem_store_credit' => rawurlencode( $coupon->get_code() ) ), $redeem_url );
}
