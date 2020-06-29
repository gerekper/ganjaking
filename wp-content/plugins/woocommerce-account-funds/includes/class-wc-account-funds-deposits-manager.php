<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Deposits_Manager
 */
class WC_Account_Funds_Deposits_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_deposit_add_to_cart', array( $this, 'add_to_cart' ) );

		// Force reg during checkout process.
		if ( ! is_admin() ) {
			add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', array( $this, 'enable_signup_and_login_from_checkout' ) );
			add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
		}

		// Topup product.
		add_filter( 'woocommerce_product_type_query', array( $this, 'woocommerce_product_type_for_topup' ), 10, 2 );
		add_filter( 'woocommerce_product_class', array( $this, 'woocommerce_product_class_for_topup' ), 10, 4 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
	}

	/**
	 * Show add to cart button
	 */
	public function add_to_cart() {
		woocommerce_simple_add_to_cart();
	}

	/**
	 * Ensure this is yes
	 */
	public function enable_signup_and_login_from_checkout( $value ) {
		remove_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
		$woocommerce_enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' );
		add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );

		if ( 'yes' === $woocommerce_enable_guest_checkout && WC_Account_Funds_Cart_Manager::cart_contains_deposit() ) {
			return 'yes';
		} else {
			return $value;
		}
	}

	/**
	 * Ensure this is no
	 */
	public function enable_guest_checkout( $value ) {
		if ( WC_Account_Funds_Cart_Manager::cart_contains_deposit() ) {
			return 'no';
		} else {
			return $value;
		}
	}

	/**
	 * Product type for topup.
	 *
	 * This filters `woocommerce_product_type_query` value introduced in WC 3.0.
	 *
	 * @since 2.1.3
	 *
	 * @version 2.1.3
	 *
	 * @param mixed $override   Product type to override. Default to false, in
	 *                          which it lookup the type from data store.
	 * @param int   $product_id Product ID.
	 *
	 * @return mixed Returns 'topup' for topup product. Otherwise false.
	 */
	public function woocommerce_product_type_for_topup( $override, $product_id ) {
		if ( wc_get_page_id( 'myaccount' ) === $product_id ) {
			return 'topup';
		}
		return $override;
	}

	/**
	 * Top up product ID = my account page ID, until WC has a filter to adjust the product object
	 */
	public function woocommerce_product_class_for_topup( $classname, $product_type, $post_type, $product_id ) {
		if ( wc_get_page_id( 'myaccount' ) === $product_id ) {
			return 'WC_Product_Topup';
		}
		return $classname;
	}

	/**
	 * Adjust the price
	 *
	 * @param mixed $cart_item
	 * @return array cart item
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['top_up_amount'] ) ) {
			$cart_item['data']->set_price( $cart_item['top_up_amount'] );
			$cart_item['variation'] = array();
		}
		return $cart_item;
	}

	/**
	 * Get data from the session and add to the cart item's meta
	 *
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return array cart item
	 */
	public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
		if ( ! empty( $values['top_up_amount'] ) ) {
			$cart_item['top_up_amount'] = $values['top_up_amount'];
			$cart_item                  = $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}
}

new WC_Account_Funds_Deposits_Manager();
