<?php

/**
 * Class WC_Dynamic_Pricing_Context
 * Keeps track of references to products in the cart.  This allows us to determine if filters called on a specific product object
 * are being called from in the context of the cart or not.
 */
class WC_Dynamic_Pricing_Context {
	/**
	 * @var WC_Dynamic_Pricing_Context
	 */
	private static $instance;

	/**
	 * Helper to bootstrap the class.
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Context();
		}
	}

	/**
	 * @return WC_Dynamic_Pricing_Context
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Context();
		}

		return self::$instance;
	}

	/**
	 * @var array Stores references to products in the cart.
	 */
	private $_products_in_cart;

	private function __construct() {
		add_filter( 'woocommerce_get_cart_item_from_session', array(
			$this,
			'on_get_cart_item_from_session'
		), 9999, 3 );
	}

	/**
	 * Record the reference to the cart item product object.
	 *
	 * @param $session_data
	 * @param $values
	 * @param $cart_item_key
	 *
	 * @return mixed
	 */
	public function on_get_cart_item_from_session( $session_data, $values, $cart_item_key ) {
		if ( isset( $session_data['discounts'] ) ) {
			unset( $session_data['discounts'] );
		}

		$this->_products_in_cart[ spl_object_hash( $session_data['data'] ) ] = $cart_item_key;

		return $session_data;
	}

	/**
	 * @param $product
	 *
	 * @return array|null Item data or null if product instance is not in the cart.
	 */
	public function get_cart_item_for_product( &$product ) {

		if ( WC()->cart ) {

			$cart_item_key = null;
			$cart_item     = null;
			$hash_key      = spl_object_hash( $product );

			if ( $this->_products_in_cart && isset( $this->_products_in_cart[ $hash_key ] ) ) {

				$cart_item_key = $this->_products_in_cart[ $hash_key ];
				$cart_item     = null;
				if ( $cart_item_key ) {
					$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				}
			}

			return $cart_item;
		} else {
			return false;
		}

	}
}