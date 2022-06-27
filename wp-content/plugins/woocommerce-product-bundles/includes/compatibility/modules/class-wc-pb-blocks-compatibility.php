<?php
/**
 * WC_PB_Blocks_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.15.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 6.15.0
 */
class WC_PB_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_PB_ABSPATH . 'includes/api/class-wc-pb-store-api.php' );
		require_once( WC_PB_ABSPATH . 'includes/blocks/class-wc-pb-checkout-blocks-integration.php' );

		WC_PB_Store_API::init();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function( $registry ) {
				$registry->register( WC_PB_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_mini-cart_block_registration',
			function( $registry ) {
				$registry->register( WC_PB_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function( $registry ) {
				$registry->register( WC_PB_Checkout_Blocks_Integration::instance() );
			}
		);
	}
}

WC_PB_Blocks_Compatibility::init();
