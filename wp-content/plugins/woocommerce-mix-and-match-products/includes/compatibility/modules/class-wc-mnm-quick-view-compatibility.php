<?php
/**
 * Quick View Compatibility
 *
 * @package  WooCommerce Name Your Price/Compatibility
 * @since    2.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_MNM_Quick_View_Compatibility class
 **/
class WC_MNM_Quick_View_Compatibility {

	/**
	 * WC_MNM_Quick_View_Compatibility Constructor
	 *
	 * @since 2.0.0
	 */
	public static function init() {

		// QuickView support.
		add_action( 'wc_quick_view_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wc_quick_view_before_single_product', array( __CLASS__, 'attach_hooks' ) );		
		
	}

	/**
	 * Load scripts for use by QV on non-product pages.
	 */
	public static function load_scripts() {

		if ( ! is_product() ) {
			wp_enqueue_script( 'wc-add-to-cart-mnm' );
			wp_enqueue_style( 'wc-mnm-frontend' );
		}
	}

	/**
	 * Add filter on the form location prop
	 */
	public static function attach_hooks() {
		add_filter( 'woocommerce_product_get_add_to_cart_form_location', array( __CLASS__, 'filter_form_location' ) );
	}

	/**
	 * Set form location prop to default in QV
	 * 
	 * @param string $location
	 * @return string
	 */
	public static function filter_form_location( $location ) {
		return 'default';
	}


} // End class: do not remove or there will be no more guacamole for you.

WC_MNM_Quick_View_Compatibility::init();
