<?php
/**
 * Integration interface.
 *
 * @package WC_Account_Funds/Interfaces
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Interface WC_Account_Funds_Integration.
 */
interface WC_Account_Funds_Integration {

	/**
	 * Initializes the integration.
	 *
	 * @since 2.5.0
	 */
	public static function init();

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename();
}
