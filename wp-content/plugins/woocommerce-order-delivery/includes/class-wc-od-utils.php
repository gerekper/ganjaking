<?php
/**
 * Utils class
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Utils' ) ) {

	class WC_OD_Utils {

		private static $active_plugins;

		/**
		 * Gets if the plugin is active.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin Base plugin path from plugins directory.
		 * @return boolean True if the plugin is active. False otherwise.
		 */
		public static function is_plugin_active( $plugin ) {
			if ( ! self::$active_plugins ) {
				self::$active_plugins = (array) get_option( 'active_plugins', array() );
				if ( is_multisite() ) {
					self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
				}
			}

			return in_array( $plugin, self::$active_plugins ) || array_key_exists( $plugin, self::$active_plugins );
		}

		/**
		 * Gets if we are in the WooCommerce settings page or not.
		 *
		 * @since 1.0.0
		 *
		 * @return boolean Are we in the WooCommerce settings page?
		 */
		public static function is_woocommerce_settings_page() {
			return ( is_admin() && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] );
		}
	}
}
