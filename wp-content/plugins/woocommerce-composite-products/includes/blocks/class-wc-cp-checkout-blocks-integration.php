<?php
/**
 * WC_CP_Checkout_Blocks_Integration class
 *
 * @package  WooCommerce Composite Products
 * @since    8.4.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks scripts.
 *
 * @version 8.5.0
 */
class WC_CP_Checkout_Blocks_Integration implements IntegrationInterface {

	/**
	 * Whether the integration has been initialized.
	 *
	 * @var boolean
	 */
	protected $is_initialized;

	/**
	 * The single instance of the class.
	 *
	 * @var WC_CP_Checkout_Blocks_Integration
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CP_Checkout_Blocks_Integration instance. Ensures only one instance of WC_CP_Checkout_Blocks_Integration is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CP_Checkout_Blocks_Integration
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
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '8.4.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '8.4.0' );
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'composite-products';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		if ( $this->is_initialized ) {
			return;
		}

		if ( is_null( WC()->cart ) ) {
			return;
		}

		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path = 'assets/dist/frontend/checkout-blocks' . $suffix . '.js';
		$script_url  = WC_CP()->plugin_url() . '/' . $script_path;

		$script_asset_path = WC_CP_ABSPATH . 'assets/dist/frontend/checkout-blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_CP()->get_file_version( WC_CP_ABSPATH . $script_path ),
			);

		wp_register_script(
			'wc-cp-checkout-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'wc-cp-checkout-blocks',
				'woocommerce-composite-products',
				dirname( WC_CP()->plugin_basename() ) . '/languages'
			);
		}

		add_action(
			'wp_enqueue_scripts',
			function() {

				$style_path = 'assets/css/frontend/checkout-blocks.css';
				$style_url  = WC_CP()->plugin_url() . '/' . $style_path;

				wp_enqueue_style(
					'wc-cp-checkout-blocks',
					$style_url,
					'',
					WC_CP()->get_file_version( WC_CP_ABSPATH . $style_path ),
					'all'
				);

				wp_style_add_data( 'wc-cp-checkout-blocks', 'rtl', 'replace' );

				$inline_css = array();

				foreach ( WC()->cart->get_cart() as $cart_item ) {
					if ( $container_item = wc_cp_get_composited_cart_item_container( $cart_item ) ) {

						$composite       = $container_item[ 'data' ];
						$component_id    = $cart_item[ 'composite_item' ];
						$component       = $composite->get_component( $component_id );
						$component_title = $component->get_title();

						$inline_css[] = 'table.wc-block-cart-items .is-composited__ctitle_' . sanitize_title( $component_title ) . ' .wc-block-cart-item__wrap:before { display: block; content:"' . $component_title . ':" }';

					} elseif ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {
						$inline_css[] = '.is-composite .wc-block-components-product-details li:not(:last-child) .wc-block-components-product-details__value { margin-bottom: 0.5em; }';
					}
				}

				if ( ! empty( $inline_css ) ) {
					wp_add_inline_style( 'wc-cp-checkout-blocks', implode( ' ', array_unique( $inline_css ) ) );
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
		return array( 'wc-cp-checkout-blocks' );
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
			'woocommerce-composite-products-checkout-blocks' => 'active',
		);
	}
}
