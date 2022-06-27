<?php
/**
 * WC_CP_Blocks_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    8.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 8.4.0
 */
class WC_CP_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_CP_ABSPATH . 'includes/api/class-wc-cp-store-api.php' );
		require_once( WC_CP_ABSPATH . 'includes/blocks/class-wc-cp-checkout-blocks-integration.php' );

		WC_CP_Store_API::init();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function( $registry ) {
				$registry->register( WC_CP_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_mini-cart_block_registration',
			function( $registry ) {
				$registry->register( WC_CP_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function( $registry ) {
				$registry->register( WC_CP_Checkout_Blocks_Integration::instance() );
			}
		);
	}
}

WC_CP_Blocks_Compatibility::init();
