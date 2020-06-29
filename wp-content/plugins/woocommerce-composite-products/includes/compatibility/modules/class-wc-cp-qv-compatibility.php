<?php
/**
 * WC_CP_QV_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.4.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuickView compatibility.
 */
class WC_CP_QV_Compatibility {

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

			WC_CP()->display->frontend_scripts();

			// Enqueue script.
			wp_enqueue_script( 'wc-add-to-cart-composite' );

			// Enqueue styles.
			wp_enqueue_style( 'wc-composite-single-css' );
		}
	}

	/**
	 * Fixes QuickView support for Composites when ajax add-to-cart is active and QuickView operates without a separate button.
	 *
	 * @param   string  $selector
	 * @return  void
	 */
	public static function qv_selector( $selector ) {

		$selector = str_replace( '.product a.product_type_variable', '.product a.product_type_variable, .product a.product_type_composite', $selector );

		return $selector;
	}
}

WC_CP_QV_Compatibility::init();
