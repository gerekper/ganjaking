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

		/**
		 * Gets the WooCommerce version.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0 Use the constant 'WC_VERSION' or the property 'WC()->version' instead.
		 *
		 * @return string The WooCommerce version.
		 */
		public static function get_woocommerce_version() {
			wc_deprecated_function( __METHOD__, '1.1.0', 'WC_VERSION or WC()->version' );

			return WC()->version;
		}

		/**
		 * Gets the menu slug for the WooCommerce settings page.
		 *
		 * @since 1.0.0
		 * @deprecated 1.4.0 No longer necessary.
		 *
		 * @return string The menu slug for the WooCommerce settings page.
		 */
		public static function get_woocommerce_settings_page_slug() {
			wc_deprecated_function( __METHOD__, '1.4.0' );

			return 'wc-settings';
		}

		/**
		 * Gets the section slug for the shipping options.
		 *
		 * @since 1.0.2
		 * @deprecated 1.4.0 No longer necessary.
		 *
		 * @return string The section slug for the shipping options.
		 */
		public static function get_shipping_options_section_slug() {
			wc_deprecated_function( __METHOD__, '1.4.0' );

			return 'options';
		}
	}
}
