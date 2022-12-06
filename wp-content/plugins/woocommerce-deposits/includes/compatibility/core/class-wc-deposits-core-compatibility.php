<?php
/**
 * WC_Deposits_Core_Compatibility class
 *
 * @package  WooCommerce Deposits
 * @since    1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions for WC core back-compatibility.
 *
 * @class    WC_Deposits_Core_Compatibility
 * @version  1.6.0
 */
class WC_Deposits_Core_Compatibility {

	/**
	 * Current REST request.
	 *
	 * @var WP_REST_Request
	 */
	private static $request;

	/**
	 * Constructor.
	 */
	public static function init() {
		// Save current rest request. Is there a better way to get it?
		add_filter( 'rest_pre_dispatch', array( __CLASS__, 'save_rest_request' ), 10, 3 );
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Saves the current rest request.
	 *
	 * @since  1.6.0
	 *
	 * @param mixed           $result REST Result.
	 * @param WP_REST_Server  $server REST Server.
	 * @param WP_REST_Request $request REST Request.
	 * @return mixed
	 */
	public static function save_rest_request( $result, $server, $request ) {
		self::$request = $request;
		return $result;
	}

	/*
	|--------------------------------------------------------------------------
	| Utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Whether this is a Store/REST API request.
	 *
	 * @since  1.6.0
	 *
	 * @return boolean
	 */
	public static function is_api_request() {
		return self::is_store_api_request() || self::is_rest_api_request();
	}

	/**
	 * Returns the current Store/REST API request or false.
	 *
	 * @since  1.6.0
	 *
	 * @return WP_REST_Request|false
	 */
	public static function get_api_request() {
		return self::$request instanceof WP_REST_Request ? self::$request : false;
	}

	/**
	 * Whether this is a Store API request.
	 *
	 * @since  1.6.0
	 *
	 * @param  string $route Route.
	 * @return boolean
	 */
	public static function is_store_api_request( $route = '' ) {

		$request = self::get_api_request();

		if ( false !== $request && strpos( $request->get_route(), 'wc/store' ) !== false ) {
			if ( '' === $route || strpos( $request->get_route(), $route ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Compatibility wrappers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Back-compat wrapper for 'is_rest_api_request'.
	 *
	 * @since  1.6.0
	 *
	 * @return boolean
	 */
	public static function is_rest_api_request() {

		if ( false !== self::get_api_request() ) {
			return true;
		}

		return method_exists( WC(), 'is_rest_api_request' ) ? WC()->is_rest_api_request() : defined( 'REST_REQUEST' );
	}

}

WC_Deposits_Core_Compatibility::init();
