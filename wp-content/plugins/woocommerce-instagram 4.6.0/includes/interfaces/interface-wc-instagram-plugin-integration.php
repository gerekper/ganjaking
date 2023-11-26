<?php
/**
 * Plugin integration interface.
 *
 * @package WC_Instagram/Interfaces
 * @since   4.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Interface WC_Instagram_Plugin_Integration.
 */
interface WC_Instagram_Plugin_Integration {

	/**
	 * Initializes the integration.
	 *
	 * @since 4.5.0
	 */
	public static function init();

	/**
	 * Gets the plugin basename.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename();
}
