<?php
/**
 * WC_PB_MMI_Module class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bundle-Sells Module
 *
 * @version  6.4.0
 */
class WC_PB_MMI_Module extends WCS_PB_Abstract_Module {

	/**
	 * Core.
	 */
	public function load_core() {

		// Admin.
		if ( is_admin() ) {
			require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/admin/class-wc-pb-mmi-admin.php' );
		}

		// Product-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/class-wc-pb-mmi-product.php' );
	}

	/**
	 * Cart.
	 */
	public function load_cart() {
		// Cart-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/class-wc-pb-mmi-cart.php' );
	}

	/**
	 * Display.
	 */
	public function load_display() {
		// Display-related functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/class-wc-pb-mmi-display.php' );
	}
}
