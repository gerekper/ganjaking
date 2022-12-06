<?php
/**
 * WC_Deposits_Blocks_Compatibility class
 *
 * @package  WooCommerce Deposits
 * @since    1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Blocks Compatibility.
 *
 * @class    WC_Deposits_Blocks_Compatibility
 * @version  1.6.0
 */
class WC_Deposits_Blocks_Compatibility {

	/**
	 * Min required plugin versions to check.
	 *
	 * @var array
	 */
	private static $required = array(
		'blocks' => '7.0.0',
	);

	/**
	 * Initialize.
	 */
	public static function init() {

		// WooCommerce Cart/Checkout Blocks support.
		if (
			! class_exists( 'Automattic\WooCommerce\Blocks\Package' )
			|| version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), self::$required['blocks'] ) <= 0
			) {
			return;
		}

		if ( ! did_action( 'woocommerce_blocks_loaded' ) ) {
			return;
		}

		require_once WC_DEPOSITS_ABSPATH . 'includes/api/class-wc-deposits-store-api.php';
		require_once WC_DEPOSITS_ABSPATH . 'includes/blocks/class-wc-deposits-blocks-integration.php';

		WC_Deposits_Store_API::initialize();

		add_action(
			'woocommerce_blocks_cart_block_registration',
			function ( $registry ) {
				$registry->register( WC_Deposits_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_mini-cart_block_registration',
			function ( $registry ) {
				$registry->register( WC_Deposits_Blocks_Integration::instance() );
			}
		);

		add_action(
			'woocommerce_blocks_checkout_block_registration',
			function ( $registry ) {
				$registry->register( WC_Deposits_Blocks_Integration::instance() );
			}
		);
	}
}

WC_Deposits_Blocks_Compatibility::init();
