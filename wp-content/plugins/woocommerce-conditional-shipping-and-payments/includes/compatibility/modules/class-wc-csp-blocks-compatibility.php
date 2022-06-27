<?php
/**
 * WC_CSP_Blocks_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.13.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @version 1.13.0
 */
class WC_CSP_Blocks_Compatibility {

	/**
	 * Initialize.
	 */
	public static function init() {

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once( WC_CSP_ABSPATH . 'includes/api/class-wc-csp-store-api.php' );
		require_once( WC_CSP_ABSPATH . 'includes/blocks/class-wc-csp-checkout-blocks-integration.php' );

		WC_CSP_Store_API::init();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function( $registry ) {
				$registry->register( new WC_CSP_Blocks_Integration() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function( $registry ) {
				$registry->register( new WC_CSP_Blocks_Integration() );
			}
		);
	}
}

WC_CSP_Blocks_Compatibility::init();
