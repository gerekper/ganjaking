<?php
/**
 * WC_MMQ_Core_Compatibility class
 *
 * @package  WooCommerce Min/Max Quantities
 * @since    2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions for WC core back-compatibility.
 *
 * @class    WC_MMQ_Core_Compatibility
 * @version  2.5.0
 */
class WC_MMQ_Core_Compatibility {

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
		// Save current rest request.
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
	 * @since  6.15.0
	 *
	 * @param  mixed            $result
	 * @param  WP_REST_Server   $server
	 * @param  WP_REST_Request  $request
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
	 * Returns the current Store/REST API request or false.
	 *
	 * @since  2.5.0
	 *
	 * @return WP_REST_Request|false
	 */
	public static function get_api_request() {
		return self::$request instanceof WP_REST_Request ? self::$request : false;
	}

	/**
	 * Whether this is a Store API request.
	 *
	 * @since  2.5.0
	 *
	 * @param  string  $route
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
}

WC_MMQ_Core_Compatibility::init();
