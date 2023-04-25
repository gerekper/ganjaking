<?php
/**
 * Integration interface.
 *
 * @package WC_OD/Interfaces
 * @since   1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Interface WC_OD_Integration.
 */
interface WC_OD_Integration {

	/**
	 * Initializes the integration.
	 *
	 * @since 1.9.0
	 */
	public static function init();

	/**
	 * Gets the plugin basename.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename();
}
