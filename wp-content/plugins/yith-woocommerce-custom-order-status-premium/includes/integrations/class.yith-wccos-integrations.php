<?php
/**
 * Integrations.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

/**
 * Integrations Class
 *
 * @class   YITH_WCCOS_Integrations
 * @since   1.1.6
 */
class YITH_WCCOS_Integrations {
	/**
	 * Single instance
	 *
	 * @var YITH_WCCOS_Integrations
	 */
	private static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCCOS_Integrations
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCCOS_Integrations constructor.
	 */
	private function __construct() {
		$integrations = array( 'wpml', 'multi-vendor', 'order-tracking', 'frontend-manager' );

		foreach ( $integrations as $integration ) {
			$file_url      = "class.yith-wccos-{$integration}-integration.php";
			$prop          = str_replace( '-', '_', $integration );
			$this->{$prop} = require_once $file_url;
		}
	}

}

/**
 * Unique access to instance of YITH_WCCOS_Integrations class
 *
 * @return YITH_WCCOS_Integrations
 */
function yith_wccos_integrations() {
	return YITH_WCCOS_Integrations::get_instance();
}
