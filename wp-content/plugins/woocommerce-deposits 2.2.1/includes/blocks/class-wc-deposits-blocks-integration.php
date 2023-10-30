<?php
/**
 * A class for integrating with WooCommerce Blocks scripts.
 *
 * @package WooCommerce Deposits
 * @since   1.6.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Blocks integration class.
 *
 * @class        WC_Deposits_Blocks_Integration
 * @implements   IntegrationInterface
 * @version      1.6.0
 */
class WC_Deposits_Blocks_Integration implements IntegrationInterface {


	/**
	 * The single instance of the class.
	 *
	 * @var WC_Deposits_Blocks_Integration
	 */
	protected static $instance = null;

	/**
	 * Main WC_Deposits_Blocks_Integration instance. Ensures only one instance of WC_Deposits_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Deposits_Blocks_Integration
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Oops!', 'woocommerce-deposits' ), '1.6.2' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Oops!', 'woocommerce-deposits' ), '1.6.2' );
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'woocommerce-deposits';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path       = '/assets/dist/frontend/blocks' . $suffix . '.js';
		$script_asset_path = WC_DEPOSITS_ABSPATH . 'assets/dist/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_DEPOSITS_VERSION,
			);
		$script_url        = WC_DEPOSITS_PLUGIN_URL . $script_path;

		wp_register_script(
			'wc-deposits-blocks',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-deposits-blocks', 'woocommerce-deposits', WC_DEPOSITS_ABSPATH . 'languages/' );
		}
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-deposits-blocks' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array( 'wc-deposits-blocks' );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array(
			'disabled_gateways' => get_option( 'wc_deposits_disabled_gateways', array() ),
		);
	}
}
