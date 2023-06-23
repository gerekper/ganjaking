<?php
/**
 * WC_Product_Addons_Blocks_Compatibility class
 *
 * @package  WooCommerce Product Add-Ons
 * @since    6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 6.4.0
 */
class WC_Product_Addons_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/api/class-wc-product-addons-store-api.php' );

		WC_Product_Addons_Store_API::init();
	}
}

WC_Product_Addons_Blocks_Compatibility::init();
