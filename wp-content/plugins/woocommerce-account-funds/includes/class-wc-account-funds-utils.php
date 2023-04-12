<?php
/**
 * Utils class.
 *
 * @package WC_Account_Funds
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Utils
 */
class WC_Account_Funds_Utils {

	/**
	 * Gets if the plugin is active.
	 *
	 * @since 2.5.0
	 *
	 * @see is_plugin_active()
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
}
