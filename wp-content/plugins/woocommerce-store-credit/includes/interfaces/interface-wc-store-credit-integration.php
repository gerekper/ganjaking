<?php
/**
 * Integration interface.
 *
 * @package WC_Store_Credit/Interfaces
 * @since   4.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Interface WC_Store_Credit_Integration.
 */
interface WC_Store_Credit_Integration {

	/**
	 * Initializes the integration.
	 *
	 * @since 4.1.0
	 */
	public static function init();

	/**
	 * Gets the plugin basename.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename();
}
