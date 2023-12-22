<?php
/**
 * Customer functions
 *
 * @package WC_Store_Credit/Functions
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the customer object.
 *
 * @since 3.0.0
 *
 * @param mixed $the_customer Customer object or ID.
 * @return WC_Customer|false The customer object. False on failure.
 */
function wc_store_credit_get_customer( $the_customer ) {
	try {
		$customer = ( $the_customer instanceof WC_Customer ? $the_customer : new WC_Customer( $the_customer ) );

		return ( 0 !== $customer->get_id() ? $customer : false );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Gets the customer email.
 *
 * @since 3.0.0
 *
 * @param mixed $the_customer Customer object, email or ID.
 * @return string|false The customer email. False on failure.
 */
function wc_store_credit_get_customer_email( $the_customer ) {
	$customer_email = ( is_email( $the_customer ) ? $the_customer : false );

	if ( ! $customer_email ) {
		$customer = wc_store_credit_get_customer( $the_customer );

		if ( $customer ) {
			$customer_email = $customer->get_email();
		}
	}

	return $customer_email;
}

/**
 * Gets the store credit coupons associated to the specified customer.
 *
 * @since 3.0.0
 * @since 4.2.0 Accepts the status 'expired'.
 *
 * @param mixed  $the_customer Customer object, email or ID.
 * @param string $status       Optional. The coupon status. Accepts: 'all', 'active', 'expired', 'exhausted'. Default: 'active'.
 * @return WC_Coupon[]|false An array with the store credit coupons. False on failure.
 */
function wc_store_credit_get_customer_coupons( $the_customer, $status = 'active' ) {
	$customer_email = wc_store_credit_get_customer_email( $the_customer );

	if ( ! $customer_email || ! in_array( $status, array( 'all', 'active', 'expired', 'exhausted' ), true ) ) {
		return false;
	}

	$cache_key  = "wc_store_credit_customer_{$status}_coupons_" . sanitize_key( $customer_email );
	$coupon_ids = wp_cache_get( $cache_key, 'store_credit' );

	if ( false === $coupon_ids ) {
		$args = array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'discount_type',
					'value' => 'store_credit',
				),
				array(
					'key'     => 'customer_email',
					'value'   => $customer_email,
					'compare' => 'LIKE',
				),
			),
		);

		if ( 'all' !== $status ) {
			$args['meta_query'][] = array(
				'key'     => 'coupon_amount',
				'value'   => 0,
				'compare' => ( 'exhausted' === $status ? '=' : '>' ),
				'type'    => 'decimal(10, ' . wc_get_price_decimals() . ')',
			);

			if ( 'expired' === $status ) {
				$args['meta_query'][] = array(
					'key'     => 'date_expires',
					'value'   => time(),
					'compare' => '<=',
					'type'    => 'NUMERIC',
				);
			} elseif ( 'active' === $status ) {
				$args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'     => 'date_expires',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => 'date_expires',
						'value'   => time(),
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				);
			}
		}

		add_filter( 'get_meta_sql', 'wc_store_credit_fix_customer_coupons_sql' );
		$coupon_ids = array_map( 'intval', get_posts( $args ) );
		add_filter( 'get_meta_sql', 'wc_store_credit_fix_customer_coupons_sql' );

		// Cache the result.
		wp_cache_set( $cache_key, $coupon_ids, 'store_credit' );
	}

	// Filter the coupons by the exact customer email to avoid false positives due to the 'LIKE' SQL query.
	$coupons = array();

	foreach ( $coupon_ids as $coupon_id ) {
		$coupon = wc_store_credit_get_coupon( $coupon_id );

		if ( ! $coupon instanceof WC_Coupon ) {
			continue;
		}

		$email_restrictions = $coupon->get_email_restrictions();

		if ( in_array( $customer_email, $email_restrictions, true ) ) {
			$coupons[] = $coupon;
		}
	}

	return $coupons;
}

/**
 * Fixes the customer coupons SQL query.
 *
 * @since 4.2.0
 *
 * @param string[] $sql Array containing the query's JOIN and WHERE clauses.
 * @return string[]
 */
function wc_store_credit_fix_customer_coupons_sql( $sql ) {
	// The coupon meta 'date_expires' exists with a NULL value.
	$sql['where'] = str_replace( '.post_id IS NULL', '.meta_value IS NULL', $sql['where'] );

	return $sql;
}

/**
 * Gets the customer label to use it in a select field.
 *
 * @since 3.1.0
 *
 * @param mixed $the_customer Customer object, email or ID.
 * @return string
 */
function wc_store_credit_get_customer_choice_label( $the_customer ) {
	$label    = $the_customer;
	$customer = wc_store_credit_get_customer( $the_customer );

	if ( $customer ) {
		$label = sprintf(
			/* translators: $1: customer name, $2 customer id, $3: customer email */
			esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce-store-credit' ),
			$customer->get_first_name() . ' ' . $customer->get_last_name(),
			$customer->get_id(),
			$customer->get_email()
		);
	}

	return $label;
}
