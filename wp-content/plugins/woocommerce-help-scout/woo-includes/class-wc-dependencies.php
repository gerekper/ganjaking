<?php
/**
 * WC_Dependencies
 *
 * @package  WC_Dependencies
 * Checks if WooCommerce is enabled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Help Scout Shortcodes.
 *
 * @package  WC_Dependencies
 */
class WC_Dependencies {
	/**
	 * Static active plugin
	 *
	 * @var active_plugins
	 * */
	private static $active_plugins;
	/**
	 * Init function.
	 */
	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}
	/**
	 * Active check function.
	 */
	public static function woocommerce_active_check() {

		if ( ! self::$active_plugins ) {
			self::init();
		}

		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
	}

}
