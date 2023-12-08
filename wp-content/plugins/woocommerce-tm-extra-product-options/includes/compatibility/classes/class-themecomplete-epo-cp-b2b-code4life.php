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
 * WooCommerce B2B by Code4Life
 * https://codecanyon.net/item/woocommerce-b2b/21565847
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_B2B_Code4Life {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_B2B_Code4Life|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
	 *
	 * @return THEMECOMPLETE_EPO_CP_B2B_Code4Life
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
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WooCommerceB2B' ) || ! function_exists( 'WCB2B' ) ) {
			return;
		}

		// Add to cart.
		add_filter( 'wc_epo_add_cart_item_original_price', [ $this, 'wc_epo_add_cart_item_original_price' ], PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_addons_price_from_session' ], PHP_INT_MAX, 1 );
		// Prices.
		add_filter( 'woocommerce_product_variation_get_price', [ $this, 'change_price' ], PHP_INT_MAX, 2 );
	}

	/**
	 * Change the product price
	 *
	 * @param float        $price The product price.
	 * @param array<mixed> $cart_item The cart item.
	 * @return array<mixed>
	 */
	public function wc_epo_add_cart_item_original_price( $price, $cart_item ) {
		return $cart_item['data']->get_price();
	}

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $cart_item The cart item.
	 * @return array<mixed>
	 */
	public function get_cart_item_addons_price_from_session( $cart_item ) {
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
	 * @return float
	 */
	public function change_price( $price, $product ) {
		if ( $product->get_meta( 'epo_price', true ) !== '' ) {
			$price = $product->get_meta( 'epo_price' );
		}
		return $price;
	}
}
