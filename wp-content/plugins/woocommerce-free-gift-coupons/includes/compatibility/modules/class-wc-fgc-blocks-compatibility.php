<?php
/**
 * WooCommerce Blocks Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    3.4.0
 * @version  3.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_FGC_Blocks_Compatibility class
 */
class WC_FGC_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_Free_Gift_Coupons::plugin_path() . '/includes/api/class-wc-fgc-store-api.php' );
		require_once( WC_Free_Gift_Coupons::plugin_path() . '/includes/blocks/class-wc-fgc-checkout-blocks-integration.php' );

		WC_FGC_Store_API::init();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function( $registry ) {
				$registry->register( WC_FGC_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_mini-cart_block_registration',
			function( $registry ) {
				$registry->register( WC_FGC_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function( $registry ) {
				$registry->register( WC_FGC_Checkout_Blocks_Integration::instance() );
			}
		);
	}
}

WC_FGC_Blocks_Compatibility::init();
