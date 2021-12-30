<?php
/**
 * WC_PB_QV_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuickView Compatibility.
 *
 * @since  4.11.4
 */
class WC_PB_QV_Compatibility {

	public static function init() {

		// QuickView support.
		add_action( 'wc_quick_view_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_filter( 'quick_view_selector', array( __CLASS__, 'qv_selector' ) );
	}

	/**
	 * Load scripts for use by QV on non-product pages.
	 */
	public static function load_scripts() {

		if ( ! is_product() ) {

			WC_PB()->display->frontend_scripts();

			wp_enqueue_script( 'wc-add-to-cart-bundle' );
			wp_enqueue_style( 'wc-bundle-css' );
		}
	}

	/**
	 * Fixes QuickView support for Bundles when ajax add-to-cart is active and QuickView operates without a separate button.
	 *
	 * @param   string  $selector
	 * @return  void
	 */
	public static function qv_selector( $selector ) {

		$selector = str_replace( '.product a.product_type_variable', '.product a.product_type_variable, .product a.product_type_bundle_input_required', $selector );

		return $selector;
	}
}

WC_PB_QV_Compatibility::init();
