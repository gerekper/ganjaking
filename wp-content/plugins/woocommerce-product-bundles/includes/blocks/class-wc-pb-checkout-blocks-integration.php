<?php
/**
 * WC_PB_Checkout_Blocks_Integration class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.15.1
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks scripts.
 *
 * @version 6.15.1
 */
class WC_PB_Checkout_Blocks_Integration implements IntegrationInterface {

	/**
	 * Whether the intregration has been initialized.
	 *
	 * @var boolean
	 */
	protected $is_initialized;

	/**
	 * The single instance of the class.
	 *
	 * @var WC_PB_Checkout_Blocks_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main WC_PB_Checkout_Blocks_Integration instance. Ensures only one instance of WC_PB_Checkout_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_PB_Checkout_Blocks_Integration
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
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '6.15.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '6.15.0' );
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'product-bundles';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		if ( $this->is_initialized ) {
			return;
		}

		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path = 'assets/dist/frontend/checkout-blocks' . $suffix . '.js';
		$script_url  = WC_PB()->plugin_url() . '/' . $script_path;

		$script_asset_path = WC_PB_ABSPATH . 'assets/dist/frontend/checkout-blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_PB()->get_file_version( WC_PB_ABSPATH . $script_path ),
			);

		wp_register_script(
			'wc-pb-checkout-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'wc-pb-checkout-blocks',
				'woocommerce-product-bundles',
				dirname( WC_PB()->plugin_basename() ) . '/languages'
			);
		}

		add_action(
			'wp_enqueue_scripts',
			function() {

				$style_path = 'assets/css/frontend/checkout-blocks.css';
				$style_url  = WC_PB()->plugin_url() . '/' . $style_path;

				wp_enqueue_style(
					'wc-pb-checkout-blocks',
					$style_url,
					'',
					WC_PB()->get_file_version( WC_PB_ABSPATH . $style_path ),
					'all'
				);
				wp_style_add_data( 'wc-pb-checkout-blocks', 'rtl', 'replace' );

				$meta_suffix = _wp_to_kebab_case( __( 'Includes', 'woocommerce-product-bundles' ) );

				if ( 'includes' !== $meta_suffix ) {
					$inline_css   = array();
					$inline_css[] = 'table.wc-block-cart-items .wc-block-cart-items__row.is-bundle__meta_hidden .wc-block-components-product-details__' . $meta_suffix . ', .wc-block-components-order-summary-item.is-bundle__meta_hidden .wc-block-components-product-details__' . $meta_suffix . ' { display:none; }';
					$inline_css[] = 'table.wc-block-cart-items .wc-block-cart-items__row.is-bundle .wc-block-components-product-details__' . $meta_suffix . ' .wc-block-components-product-details__name, .wc-block-components-order-summary-item.is-bundle .wc-block-components-product-details__' . $meta_suffix . ' .wc-block-components-product-details__name { display:block; margin-bottom: 0.5em }';
					$inline_css[] = 'table.wc-block-cart-items .wc-block-cart-items__row.is-bundle .wc-block-components-product-details__' . $meta_suffix . ':not(:first-of-type) .wc-block-components-product-details__name, .wc-block-components-order-summary-item.is-bundle .wc-block-components-product-details__' . $meta_suffix . ':not(:first-of-type) .wc-block-components-product-details__name { display:none }';
					$inline_css[] = 'table.wc-block-cart-items .wc-block-cart-items__row.is-bundle .wc-block-components-product-details__' . $meta_suffix . ' + li:not( .wc-block-components-product-details__' . $meta_suffix . ' ), .wc-block-components-order-summary-item.is-bundle .wc-block-components-product-details__' . $meta_suffix . ' + li:not( .wc-block-components-product-details__' . $meta_suffix . ' ) { margin-top:0.5em }';
					wp_add_inline_style( 'wc-pb-checkout-blocks', implode( ' ' , $inline_css ) );
				}
			}
		);

		$this->is_initialized = true;
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-pb-checkout-blocks' );
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
			'woocommerce-product-bundles-checkout-blocks' => 'active',
		);
	}
}
