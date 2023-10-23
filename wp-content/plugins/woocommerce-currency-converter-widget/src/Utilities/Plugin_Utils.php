<?php
/**
 * Plugin utilities.
 *
 * @since 1.7.0
 */

namespace KoiLab\WC_Currency_Converter\Utilities;

/**
 * Class Plugin_Utils.
 */
class Plugin_Utils {

	/**
	 * Cache the active plugins.
	 *
	 * @var array
	 */
	private static $active_plugins;

	/**
	 * Gets the active plugins.
	 *
	 * @since 1.7.0
	 *
	 * @return array
	 */
	public static function get_active_plugins() {
		if ( ! self::$active_plugins ) {
			self::$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}
		}

		return self::$active_plugins;
	}

	/**
	 * Gets if the plugin is active.
	 *
	 * @since 1.7.0
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		$active_plugins = self::get_active_plugins();

		return ( in_array( $plugin, $active_plugins, true ) || array_key_exists( $plugin, $active_plugins ) );
	}

	/**
	 * Gets if the WooCommerce plugin is active.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		return self::is_plugin_active( 'woocommerce/woocommerce.php' );
	}
}
