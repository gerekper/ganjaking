<?php
/**
 * Logger for OAuth2.0 testing and developement.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Logger class for OAuth2.0 testing and developement.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */
class WC_XR_Oauth20_Logger extends WC_XR_Logger {

	/**
	 * Override constructor to not need the settings object.
	 */
	public function __construct() {}
	/**
	 * This can be enabled only in code. Used for advanced OAuth20 debugging.
	 * Do not use in production.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'wc_xero_oauth20_logging', false );
	}
}
