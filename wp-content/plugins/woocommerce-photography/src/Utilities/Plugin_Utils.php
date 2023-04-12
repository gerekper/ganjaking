<?php
/**
 * Plugin utilities.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Photography\Utilities;

/**
 * Class Plugin_Utils.
 */
class Plugin_Utils {

	/**
	 * Gets if the plugin is active.
	 *
	 * @since 1.2.0
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( $plugin );
	}

	/**
	 * Gets if the WooCommerce plugin is active.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		return self::is_plugin_active( 'woocommerce/woocommerce.php' );
	}
}
