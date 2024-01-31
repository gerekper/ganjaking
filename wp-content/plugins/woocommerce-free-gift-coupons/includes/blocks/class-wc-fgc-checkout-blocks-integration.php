<?php
/**
 * WC_FGC_Checkout_Blocks_Integration class
 *
 * @package  WooCommerce Free Gift Coupons/Blocks
 * @since    3.4.0
 * @version  3.5.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks scripts.
 */
class WC_FGC_Checkout_Blocks_Integration implements IntegrationInterface {

	/**
	 * Whether the intregration has been initialized.
	 *
	 * @var boolean
	 */
	protected $is_initialized;

	/**
	 * The single instance of the class.
	 *
	 * @var WC_FGC_Checkout_Blocks_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main WC_FGC_Checkout_Blocks_Integration instance. Ensures only one instance of WC_FGC_Checkout_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_FGC_Checkout_Blocks_Integration
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning this object is forbidden.', 'wc_free_gift_coupons' ), '3.5.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'wc_free_gift_coupons' ),'3.5.0' );
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'free_gift_coupons/checkout';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		if ( $this->is_initialized ) {
			return;
		}

		$script_path = 'assets/dist/frontend/checkout-blocks.js';
		$script_url  =  WC_Free_Gift_Coupons::plugin_url() . '/' . $script_path;

		$script_asset_path =  WC_Free_Gift_Coupons::plugin_path() . '/assets/dist/frontend/checkout-blocks.asset.php';

		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_Free_Gift_Coupons::get_file_version( WC_Free_Gift_Coupons::plugin_path() . $script_path ),
			);

		wp_register_script(
			'wc-fgc-checkout-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'wc-fgc-checkout-blocks',
				'wc_free_gift_coupons',
				dirname( plugin_basename( WC_FGC_PLUGIN_FILE ) ) . '/languages'
			);
		}

		$this->is_initialized = true;
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-fgc-checkout-blocks' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array();
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array(
			'wc_free_gift_coupons-checkout-blocks' => 'active',
		);
	}
}
