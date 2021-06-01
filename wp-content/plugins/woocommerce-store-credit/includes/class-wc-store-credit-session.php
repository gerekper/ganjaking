<?php
/**
 * Class to handle the store credit coupons in the customer session.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Session class.
 */
class WC_Store_Credit_Session {

	/**
	 * Gets the store credit coupons from the session.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	public static function get_coupons() {
		return WC()->session->get( 'store_credit_coupons', array() );
	}

	/**
	 * Adds a store credit coupon to the session.
	 *
	 * @since 3.7.0
	 *
	 * @param string $code Coupon code.
	 */
	public static function add_coupon( $code ) {
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		$coupons = self::get_coupons();

		if ( ! in_array( $code, $coupons, true ) ) {
			$coupons[] = $code;

			WC()->session->set( 'store_credit_coupons', $coupons );
		}
	}

	/**
	 * Remove all store credit coupons from the session.
	 *
	 * @since 3.7.0
	 */
	public static function clear_coupons() {
		WC()->session->set( 'store_credit_coupons', null );
	}
}
