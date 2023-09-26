<?php
/**
 * WCS_ATT_Integration_Blocks class
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    3.3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 3.3.2
 */
class WCS_ATT_Integration_Blocks {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WCS_ATT_ABSPATH . 'includes/api/class-wcs-att-store-api.php' );

		WCS_ATT_Store_API::init();
	}
}

WCS_ATT_Integration_Blocks::init();
