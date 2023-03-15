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

		/**
		 * Gets if the plugin is active.
		 *
		 * @since 1.0.0
		 *
		 * @see is_plugin_active()
		 *
		 * @param string $plugin Base plugin path from plugins directory.
		 * @return boolean True if the plugin is active. False otherwise.
		 */
		public static function is_plugin_active( $plugin ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( $plugin );
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
