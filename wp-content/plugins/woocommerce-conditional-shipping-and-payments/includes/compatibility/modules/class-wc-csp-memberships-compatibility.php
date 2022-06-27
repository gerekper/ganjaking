<?php
/**
 * WC_CSP_Memberships_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Memberships Compatibility.
 *
 * @since  1.4.0
 */
class WC_CSP_Memberships_Compatibility {

	/**
	 * Initialization.
	 */
	public static function init() {
		self::load_conditions();
	}

	/**
	 * Load additional conditions by adding to the global conditions array.
	 *
	 * @return void
	 */
	public static function load_conditions() {

		$load_conditions = array(
			'WC_CSP_Condition_Membership_Plan'
		);

		if ( is_array( WC_CSP()->conditions->conditions ) ) {

			foreach ( $load_conditions as $condition ) {

				$condition = new $condition();
				WC_CSP()->conditions->conditions[ $condition->id ] = $condition;
			}
		}
	}
}

WC_CSP_Memberships_Compatibility::init();
