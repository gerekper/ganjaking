<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Booster Prices and Currencies by Country Module Options
 * https://booster.io/
 *
 * @package Extra Product Options/Compatibility
 * @version 5.0.12.13
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_Booster {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ), 2 );
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
		    // Add to cart
			add_filter( 'wc_epo_add_cart_item_original_price',             array( $this, 'wc_epo_add_cart_item_original_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session',     array( $this, 'get_cart_item_addons_price_from_session' ), PHP_INT_MAX, 3 );
			// Prices
			add_filter( WCJ_PRODUCT_GET_PRICE_FILTER,                 array( $this, 'change_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_product_variation_get_price',    array( $this, 'change_price' ), PHP_INT_MAX, 2 );
        }
	}

    public function wc_epo_add_cart_item_original_price( $price, $cart_item ) {
		return $cart_item['data']->get_price();
	}

	public function get_cart_item_addons_price_from_session( $cart_item, $values, $cart_item_key ) {
		if ( array_key_exists( 'tm_epo_set_product_price_with_options', $cart_item ) ) {
			$cart_item['data']->add_meta_data( 'epo_price', $cart_item['tm_epo_set_product_price_with_options'] );
		}

		return $cart_item;
	}

	public function change_price( $price, $_product ) {
        if ( $_product->get_meta( 'epo_price', true ) !== '' ) {
            $price = $_product->get_meta( 'epo_price' );
        }
		return $price;
	}

}
