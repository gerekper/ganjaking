<?php
/**
 * WC_MNM_Blocks_Compatibility class
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 2.0.0
 */
class WC_MNM_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once WC_Mix_and_Match()->plugin_path() . '/includes/api/class-wc-mnm-store-api.php';
		require_once WC_Mix_and_Match()->plugin_path() . '/includes/blocks/class-wc-mnm-checkout-blocks-integration.php';

		WC_MNM_Store_API::init();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function ( $registry ) {
				$registry->register( WC_MNM_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_mini-cart_block_registration',
			function ( $registry ) {
				$registry->register( WC_MNM_Checkout_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function ( $registry ) {
				$registry->register( WC_MNM_Checkout_Blocks_Integration::instance() );
			}
		);
	}
}

WC_MNM_Blocks_Compatibility::init();
