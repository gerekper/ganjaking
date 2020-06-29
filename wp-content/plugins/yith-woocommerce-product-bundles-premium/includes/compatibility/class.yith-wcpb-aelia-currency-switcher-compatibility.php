<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Aelia Currency Switcher Compatibility Class
 *
 * @class   YITH_WCPB_Aelia_Currency_Switcher_Compatibility
 * @since   1.3.8
 */
class YITH_WCPB_Aelia_Currency_Switcher_Compatibility {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCPB_Aelia_Currency_Switcher_Compatibility
	 */
	protected static $instance;

	/**
	 * @var WC_Aelia_CurrencyPrices_Manager The object that handles Currency Prices for the Products.
	 */
	protected static $_currencyprices_manager;

	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCPB_Aelia_Currency_Switcher_Compatibility
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	protected function __construct() {
		add_filter( 'wc_aelia_currencyswitcher_product_convert_callback', array( $this, 'wc_aelia_currencyswitcher_product_convert_callback' ), 10, 2 );
		add_action( 'woocommerce_process_product_meta_yith_bundle', array( $this, 'woocommerce_process_product_meta_yith_bundle' ) );
	}

	/**
	 * Returns the instance of the currency prices manager class.
	 *
	 * @return WC_Aelia_CurrencyPrices_Manager
	 */
	protected static function currencyprices_manager() {
		if ( empty( self::$_currencyprices_manager ) ) {
			self::$_currencyprices_manager = \WC_Aelia_CurrencyPrices_Manager::instance();
		}

		return self::$_currencyprices_manager;
	}

	/**
	 * Indicates if a bundle is priced on a per product basis.
	 *
	 * @param WC_Product product The bundle product to check.
	 *
	 * @return bool
	 */
	protected function is_bundle_priced_individually( $product ) {
		return ! empty( $product->per_items_pricing );
	}

	/**
	 * Callback to perform the conversion of bundle prices into selected currency.
	 *
	 * @param callable $convert_callback A callable, or null.
	 * @param WC_Product The product to examine.
	 *
	 * @return callable
	 */
	public function wc_aelia_currencyswitcher_product_convert_callback( $convert_callback, $product ) {
		$method_keys = array(
			'WC_Product_Yith_Bundle' => 'yith_bundle',
		);

		// Determine the conversion method to use
		$method_key     = isset( $method_keys[ get_class( $product ) ] ) ? $method_keys[ get_class( $product ) ] : '';
		$convert_method = 'convert_' . $method_key . '_product_prices';

		if ( ! method_exists( $this, $convert_method ) ) {
			return $convert_callback;
		}

		return array( $this, $convert_method );
	}

	/**
	 * Indicates if a product requires conversion.
	 *
	 * @param WC_Product product The product to process.
	 * @param string currency The target currency for which product prices will
	 * be requested.
	 *
	 * @return bool
	 */
	protected function product_requires_conversion( $product, $currency ) {
		// If the product is already in the target currency, it doesn't require
		// conversion
		return empty( $product->currency ) || ( $product->currency != $currency );
	}

	/**
	 * Converts the prices of a bundle product to the specified currency.
	 *
	 * @param WC_Product_Yith_Bundle product A variable product.
	 * @param string currency A currency code.
	 *
	 * @return WC_Product_Yith_Bundle The product with converted prices.
	 */
	public function convert_yith_bundle_product_prices( $product, $currency ) {
		if ( ! $this->product_requires_conversion( $product, $currency ) ) {
			return $product;
		}

		if ( ! $this->is_bundle_priced_individually( $product ) ) {
			$product = self::currencyprices_manager()->convert_simple_product_prices( $product, $currency );
		}

		return $product;
	}

	/*** Manual pricing of bundles ***/
	/**
	 * Event handler fired when a bundle is being saved. It processes and
	 * saves the Currency Prices associated with the bundle.
	 *
	 * @param int post_id The ID of the Post (bundle) being saved.
	 */
	public function woocommerce_process_product_meta_yith_bundle( $post_id ) {
		self::currencyprices_manager()->process_product_meta( $post_id );
	}

}