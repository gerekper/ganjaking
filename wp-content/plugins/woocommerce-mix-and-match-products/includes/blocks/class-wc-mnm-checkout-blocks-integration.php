<?php
/**
 * WC_MNM_Checkout_Blocks_Integration class
 *
 * @package  WooCommerce Mix and Match Products/Blocks
 * @since    2.0.0
 * @version  2.3.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks scripts.
 */
class WC_MNM_Checkout_Blocks_Integration implements IntegrationInterface {

	/**
	 * Whether the intregration has been initialized.
	 *
	 * @var boolean
	 */
	protected $is_initialized;

	/**
	 * The single instance of the class.
	 *
	 * @var WC_MNM_Checkout_Blocks_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main WC_MNM_Checkout_Blocks_Integration instance. Ensures only one instance of WC_MNM_Checkout_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_MNM_Checkout_Blocks_Integration
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
		_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'woocommerce-mix-and-match-products' ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-mix-and-match-products' ) );
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mix_and_match';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		if ( $this->is_initialized ) {
			return;
		}

		$script_path = 'assets/dist/frontend/checkout-blocks.js';
		$script_url  = WC_Mix_and_Match()->plugin_url() . '/' . $script_path;

		$script_asset_path = WC_Mix_and_Match()->plugin_path() . '/assets/dist/frontend/checkout-blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_Mix_and_Match()->get_file_version( WC_Mix_and_Match()->plugin_path() . $script_path ),
			);

		wp_register_script(
			'wc-mnm-checkout-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'wc-mnm-checkout-blocks',
				'woocommerce-mix-and-match-products',
				dirname( WC_Mix_and_Match()->plugin_basename() ) . '/languages'
			);
		}

		add_action(
			'wp_enqueue_scripts',
			function() {

				$style_path = '/assets/css/frontend/blocks/checkout-blocks.css';
				$style_url  = WC_Mix_and_Match()->plugin_url() . $style_path;

				wp_enqueue_style(
					'wc-mnm-checkout-blocks',
					$style_url,
					'',
					WC_Mix_and_Match()->get_file_version( WC_Mix_and_Match()->plugin_path() . $style_path ),
					'all'
				);
				wp_style_add_data( 'wc-mnm-checkout-blocks', 'rtl', 'replace' );

				// Classnames are statically generated in WC 7.3 using Woo Blocks 9.1. @see: https://github.com/woocommerce/woocommerce/pull/35876
				if ( ! WC_MNM_Core_Compatibility::is_wc_version_gte( '7.3' ) ) {

					$meta_suffix = _wp_to_kebab_case( esc_html__( 'Selections', 'woocommerce-mix-and-match-products' ) );

					$inline_css   = array();

					if ( 'selections' !== $meta_suffix ) {
						$inline_css[] = 'table.wc-block-cart-items .wc-block-cart-items__row.is-mnm-container .wc-block-components-product-details__' . $meta_suffix . ' .wc-block-components-product-details__name { display:none; }';
						$inline_css[] = '.wc-block-components-order-summary-item.is-mnm-container .wc-block-components-product-details__' . $meta_suffix . ' .wc-block-components-product-details__name { display:block; margin-bottom: 0.5em; font-weight: bold; }';
						$inline_css[] = '.wc-block-components-order-summary-item.is-mnm-container .wc-block-components-product-details__' . $meta_suffix . ':not(:first-of-type) .wc-block-components-product-details__name { display:none }';
						$inline_css[] = '.wc-block-components-order-summary-item.is-mnm-container .wc-block-components-product-details__' . $meta_suffix . ' + li:not( .wc-block-components-product-details__' . $meta_suffix . ' ) { margin-top:0.5em }';
					}

					wp_add_inline_style( 'wc-mnm-checkout-blocks', implode( ' ' , $inline_css ) );

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
		return array( 'wc-mnm-checkout-blocks' );
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
			'woocommerce-mix-and-match-products-checkout-blocks' => 'active',
		);
	}
}
