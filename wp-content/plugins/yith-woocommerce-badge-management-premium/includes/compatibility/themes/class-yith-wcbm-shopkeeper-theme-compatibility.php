<?php
/**
 * Shopkeeper Theme Compatibility Class
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

/**
 * Shopkeeper Theme Compatibility Class
 *
 * @since   1.3.7
 */
class YITH_WCBM_Shopkeeper_Theme_Compatibility {
	/**
	 * Instance
	 *
	 * @var YITH_WCBM_Shopkeeper_Theme_Compatibility
	 */
	protected static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCBM_Shopkeeper_Theme_Compatibility
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCBM_Shopkeeper_Compatibility constructor.
	 */
	private function __construct() {
		if ( ! is_admin() ) {
			remove_filter( 'woocommerce_single_product_image_thumbnail_html', array( YITH_WCBM_Frontend(), 'show_badge_on_product_thumbnail' ), 99 );
		}
	}
}
