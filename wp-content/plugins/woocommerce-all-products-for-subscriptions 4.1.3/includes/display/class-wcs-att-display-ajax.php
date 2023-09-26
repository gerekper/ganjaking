<?php
/**
 * WCS_ATT_Display_Ajax class
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles AJAX front-end requests.
 *
 * @class    WCS_ATT_Display_Ajax
 * @version  4.0.0
 */
class WCS_ATT_Display_Ajax {

	/**
	 * Initialization.
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * Hook-in.
	 */
	private static function add_hooks() {}

	/*
	|--------------------------------------------------------------------------
	| Deprecated
	|--------------------------------------------------------------------------
	*/

	/**
	 * Ajax handler for saving the subscription scheme chosen at cart-level.
	 *
	 * @deprecated 4.0.0
	 *
	 * @return void
	 */
	public static function update_cart_subscription_scheme() {
		_deprecated_function( __METHOD__ . '()', '4.0.0' );
	}
}

WCS_ATT_Display_Ajax::init();
