<?php

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks scripts
 *
 * @package WooCommerce Conditional Shipping and Payments
 * @since   1.13.0
 * @version 1.15.0
 */
class WC_CSP_Blocks_Integration implements IntegrationInterface {


	/**
	 * The single instance of the class.
	 *
	 * @var WC_CSP_Blocks_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CSP_Blocks_Integration instance. Ensures only one instance of WC_CSP_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CSP_Blocks_Integration
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0');
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'woocommerce-conditional-shipping-and-payments';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path       = '/assets/dist/frontend/blocks' . $suffix . '.js';
		$script_asset_path = WC_CSP_ABSPATH . 'assets/dist/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => WC_CSP()->plugin_version()
			);
		$script_url        = WC_CSP()->plugin_url() . $script_path;

		wp_register_script(
			'wc-csp-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-csp-blocks', 'woocommerce-conditional-shipping-and-payments', WC_CSP_ABSPATH . 'languages/' );
		}
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-csp-blocks' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array( 'wc-csp-blocks' );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {

		$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );

		remove_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );
		$all_gateways = WC()->payment_gateways->get_available_payment_gateways();
		add_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );

		global $post;
		$is_singular = true;
		if ( ! is_a( $post, 'WP_Post' ) ) {
			$is_singular = false;
		}

		$script_data = array(
			'gateways'            => array_keys( $all_gateways ),
			'is_cart'             => $is_singular ? has_block( 'woocommerce/cart', $post ) : false,
			'is_debugger_enabled' => (bool) wc_csp_debug_enabled(),
		);

		return $script_data;
	}
}
