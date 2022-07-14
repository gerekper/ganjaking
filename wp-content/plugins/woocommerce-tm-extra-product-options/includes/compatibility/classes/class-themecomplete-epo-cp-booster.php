<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Booster Prices and Currencies by Country Module Options
 * https://booster.io/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Booster {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Booster|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ], 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WCJ_Price_by_Country_Core' ) || ! function_exists( 'wcj_is_module_enabled' ) ) {
			return;
		}

		if ( wcj_is_module_enabled( 'price_by_country' ) ) {
			// Add to cart.
			add_filter( 'wc_epo_add_cart_item_original_price', [ $this, 'wc_epo_add_cart_item_original_price' ], PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_addons_price_from_session' ], PHP_INT_MAX, 3 );
			// Prices.
			if ( defined( 'WCJ_PRODUCT_GET_PRICE_FILTER' ) ) {
				add_filter( WCJ_PRODUCT_GET_PRICE_FILTER, [ $this, 'change_price' ], PHP_INT_MAX, 2 );
			}
			add_filter( 'woocommerce_product_variation_get_price', [ $this, 'change_price' ], PHP_INT_MAX, 2 );
		}
	}

	/**
	 * Change the product price
	 *
	 * @param float $price The product price.
	 * @param array $cart_item The cart item.
	 */
	public function wc_epo_add_cart_item_original_price( $price, $cart_item ) {
		return $cart_item['data']->get_price();
	}

	/**
	 * Undocumented function
	 *
	 * @param array  $cart_item The cart item.
	 * @param array  $values The saved values.
	 * @param string $cart_item_key The cart item key.
	 * @return array
	 */
	public function get_cart_item_addons_price_from_session( $cart_item, $values, $cart_item_key ) {
		if ( array_key_exists( 'tm_epo_set_product_price_with_options', $cart_item ) ) {
			$cart_item['data']->add_meta_data( 'epo_price', $cart_item['tm_epo_set_product_price_with_options'] );
		}

		return $cart_item;
	}

	/**
	 * Alter the product price
	 *
	 * @param float  $price The product price.
	 * @param object $product The product object.
	 */
	public function change_price( $price, $product ) {
		if ( $product->get_meta( 'epo_price', true ) !== '' ) {
			$price = $product->get_meta( 'epo_price' );
		}
		return $price;
	}

}
