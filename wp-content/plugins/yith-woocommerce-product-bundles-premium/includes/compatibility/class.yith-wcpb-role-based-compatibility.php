<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * YITH WooCommerce Role Based Pricing Compatibility Class
 *
 * @class   YITH_WCPB_Role_Based_Compatibility
 * @package Yithemes
 * @since   1.1.1
 * @author  Yithemes
 */
class YITH_WCPB_Role_Based_Compatibility {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCPB_Role_Based_Compatibility
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCPB_Role_Based_Compatibility
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		$regular_price_hooks = array(
			'add'    => array(
				'yith_wcpb_before_bundle_items_list',
				'yith_wcpb_get_product_variations_before',
				'yith_wcpb_before_bundle_woocommerce_cart_item_price',
				'yith_wcpb_before_bundle_bundles_item_subtotal',
				'yith_wcpb_before_bundle_woocommerce_add_cart_item',
				'yith_wcpb_before_bundle_woocommerce_get_cart_item_from_session_bundled_by',
				'yith_wcpb_before_get_per_item_price_tot',
				'yith_wcpb_before_get_per_item_price_tot_max',
			),
			'remove' => array(
				'yith_wcpb_after_bundle_items_list',
				'yith_wcpb_get_product_variations_after',
				'yith_wcpb_after_bundle_woocommerce_cart_item_price',
				'yith_wcpb_after_bundle_bundles_item_subtotal',
				'yith_wcpb_after_bundle_woocommerce_add_cart_item',
				'yith_wcpb_after_bundle_woocommerce_get_cart_item_from_session_bundled_by',
				'yith_wcpb_after_get_per_item_price_tot',
				'yith_wcpb_after_get_per_item_price_tot_max',
			),
		);

		foreach ( $regular_price_hooks as $action => $hooks ) {
			foreach ( $hooks as $hook ) {
				add_action( $hook, array( $this, $action . '_regular_price_actions' ) );
			}
		}


		$variation_price_hooks = array(
			'add'    => array(
				'yith_wcpb_before_get_per_item_price_tot',
				'yith_wcpb_before_get_per_item_price_tot_max',
				'yith_wcpb_before_bundled_item_construct',
				'yith_wcpb_before_get_per_item_price_tot_with_params',
			),
			'remove' => array(
				'yith_wcpb_after_get_per_item_price_tot',
				'yith_wcpb_after_get_per_item_price_tot_max',
				'yith_wcpb_after_bundled_item_construct',
				'yith_wcpb_after_get_per_item_price_tot_with_params',
			),
		);

		foreach ( $variation_price_hooks as $action => $hooks ) {
			foreach ( $hooks as $hook ) {
				add_action( $hook, array( $this, $action . '_regular_price_and_variations_regular_price_actions' ) );
			}
		}

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'add_bundled_attribute' ) );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'remove_bundled_attribute' ) );

		add_filter( 'yith_wcrbp_return_original_price', array( $this, 'return_original_price' ), 10, 3 );
		add_filter( 'yith_wcrbp_get_role_based_price', array( $this, 'get_role_based_price' ), 10, 2 );
	}

	/**
	 * @param bool       $value
	 * @param string     $price
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
	public function return_original_price( $value, $price, $product ) {
		return $product->is_type( 'yith_bundle' ) && isset( $product->per_items_pricing ) && $product->per_items_pricing;
	}

	public function get_role_based_price( $price, $product ) {
		if ( isset( $product->yith_wcpb_discount ) ) {
			$discount = absint( $product->yith_wcpb_discount ) / 100 * $price;
			$discount = apply_filters( 'yith_wcpb_bundled_item_calculated_discount', absint( $product->yith_wcpb_discount ) / 100 * $price, absint( $product->yith_wcpb_discount ), $price, $product->get_id(), array() );
			$price    = $price - $discount;
			$price    = yith_wcpb_round_bundled_item_price( $price );
		}

		if ( isset( $product->bundled_item_price_zero ) ) {
			$price = 0;
		}

		return $price;
	}

	public function add_regular_price_actions() {
		add_filter( 'woocommerce_product_get_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 20, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 20, 2 );
		add_filter( 'woocommerce_get_variation_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_variation_role_price' ), 20, 4 );
	}

	public function remove_regular_price_actions() {
		remove_filter( 'woocommerce_product_get_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 20 );
		remove_filter( 'woocommerce_product_variation_get_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 20 );
		remove_filter( 'woocommerce_get_variation_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_variation_role_price' ), 20 );
	}

	public function add_regular_price_and_variations_regular_price_actions() {
		$this->add_regular_price_actions();
		add_filter( 'woocommerce_variation_prices_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 5, 2 );

	}

	public function remove_regular_price_and_variations_regular_price_actions() {
		$this->remove_regular_price_actions();
		remove_filter( 'woocommerce_variation_prices_regular_price', array( YITH_Role_Based_Prices_Product(), 'get_price' ), 5 );
	}

	/**
	 * add bundled discount attribute to apply discount on subtotals and totals
	 *
	 * @param WC_Cart $cart_obj
	 */
	public function add_bundled_attribute( $cart_obj ) {
		$cart = $cart_obj->get_cart();

		foreach ( $cart as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['bundled_by'] ) ) {
				$cart_item['data']->yith_wcpb_discount = isset( $cart_item['discount'] ) ? $cart_item['discount'] : 0;
			}
		}

	}

	/**
	 * remove bundled discount attribute to prevent issues in cart
	 *
	 * @param WC_Cart $cart_obj
	 */
	public function remove_bundled_attribute( $cart_obj ) {
		$cart = $cart_obj->get_cart();

		foreach ( $cart as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['bundled_by'] ) && isset( $cart_item['data']->yith_wcpb_discount ) ) {
				unset( $cart_item['data']->yith_wcpb_discount );
			}
		}

	}
}

/**
 * Unique access to instance of YITH_WCPB_Role_Based_Compatibility class
 *
 * @return YITH_WCPB_Role_Based_Compatibility
 * @since 1.0.11
 */
function YITH_WCPB_Role_Based_Compatibility() {
	return YITH_WCPB_Role_Based_Compatibility::get_instance();
}