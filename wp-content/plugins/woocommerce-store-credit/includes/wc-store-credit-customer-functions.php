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
 *
 * @param mixed  $the_customer Customer object, email or ID.
 * @param string $status       Optional. The coupon status. Accepts: 'all', 'active', 'exhausted'. Default: 'active'.
 * @return array|false An array with the store credit coupons. False on failure.
 */
function wc_store_credit_get_customer_coupons( $the_customer, $status = 'active' ) {
	$customer_email = wc_store_credit_get_customer_email( $the_customer );

	if ( ! $customer_email || ! in_array( $status, array( 'all', 'active', 'exhausted' ), true ) ) {
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
				'compare' => ( 'active' === $status ? '>' : '=' ),
			);
		}

		$coupon_ids = array_map( 'intval', get_posts( $args ) );

		// Cache the result.
		wp_cache_set( $cache_key, $coupon_ids, 'store_credit' );
	}

	$coupons = array();

	if ( ! empty( $coupon_ids ) ) {
		$coupons = array_filter( array_map( 'wc_store_credit_get_coupon', $coupon_ids ) );
	}

	return $coupons;
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
