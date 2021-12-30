<?php
/**
 * WC_PB_BS_Module class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bundle-Sells Module
 *
 * @version  5.8.0
 */
class WC_PB_BS_Module extends WCS_PB_Abstract_Module {

	/**
	 * Core.
	 */
	public function load_core() {

		// Admin.
		if ( is_admin() ) {
			require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/admin/class-wc-pb-bs-admin.php' );
		}

		// Global-scope functions.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/wc-pb-bs-functions.php' );

		// Product-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/class-wc-pb-bs-product.php' );

		// REST API hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/class-wc-pb-bs-rest-api.php' );
	}

	/**
	 * Cart.
	 */
	public function load_cart() {
		// Cart-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/class-wc-pb-bs-cart.php' );
	}

	/**
	 * Order.
	 */
	public function load_order() {
		// Order-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/class-wc-pb-bs-order.php' );
	}

	/**
	 * Display.
	 */
	public function load_display() {
		// Display-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/bundle-sells/includes/class-wc-pb-bs-display.php' );
	}
}
