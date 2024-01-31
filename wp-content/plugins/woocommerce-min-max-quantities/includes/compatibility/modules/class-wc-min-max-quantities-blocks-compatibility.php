<?php
/**
 * WC_MMQ_Blocks_Compatibility class
 *
 * @package  Woo Min/Max Quantities
 * @since    4.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 4.0.4
 */
class WC_MMQ_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_MMQ_ABSPATH . 'includes/api/class-wc-mmq-store-api.php' );

		WC_MMQ_Store_API::init();
	}
}

WC_MMQ_Blocks_Compatibility::init();
