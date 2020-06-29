<?php
/**
 * WC_PB_BS_Cart class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart-related functions and filters.
 *
 * @class    WC_PB_BS_Cart
 * @version  6.0.0
 */
class WC_PB_BS_Cart {

	/**
	 * Internal flag for bypassing filters.
	 *
	 * @var array
	 */
	private static $bypass_filters = array();

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Validate bundle-sell add-to-cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'validate_add_to_cart' ), 100, 6 );

		// Add bundle-sells to the cart. Must run before WooCommerce sets the session data on 'woocommerce_add_to_cart' (20).
		add_action( 'woocommerce_add_to_cart', array( __CLASS__, 'bundle_sells_add_to_cart' ), 15, 6 );

		// Filter the add-to-cart success message.
		add_filter( 'wc_add_to_cart_message_html', array( __CLASS__, 'bundle_sells_add_to_cart_message_html' ), 10, 2 );

		if ( 'filters' === WC_PB_Product_Prices::get_bundled_cart_item_discount_method() ) {
			// Allow bundle-sells discounts to be applied.
			add_filter( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'load_bundle_sells_from_session' ), 10 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Application layer functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the posted bundle-sells configuration of a product.
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_posted_bundle_sells_configuration( $product ) {

		if ( ! ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( $product );
		}

		$bundle_sells_add_to_cart_configuration = array();

		// Any bundle-sell IDs present?
		$bundle_sell_ids = WC_PB_BS_Product::get_bundle_sell_ids( $product );

		if ( ! empty( $bundle_sell_ids ) ) {

			// Construct a dummy bundle to collect the posted form content.
			$bundle        = WC_PB_BS_Product::get_bundle( $bundle_sell_ids, $product );
			$bundled_items = $bundle->get_bundled_items();
			$configuration = WC_PB()->cart->get_posted_bundle_configuration( $bundle );

			foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

				if ( isset( $configuration[ $bundled_item_id ] ) ) {
					$bundled_item_configuration = $configuration[ $bundled_item_id ];
				} else {
					continue;
				}

				if ( isset( $bundled_item_configuration[ 'optional_selected' ] ) && 'no' === $bundled_item_configuration[ 'optional_selected' ] ) {
					continue;
				}

				if ( isset( $bundled_item_configuration[ 'quantity' ] ) && absint( $bundled_item_configuration[ 'quantity' ] ) === 0 ) {
					continue;
				}

				$bundle_sell_quantity = isset( $bundled_item_configuration[ 'quantity' ] ) ? absint( $bundled_item_configuration[ 'quantity' ] ) : $bundled_item->get_quantity();

				$bundle_sells_add_to_cart_configuration[ $bundled_item_id ] = array(
					'product_id' => $bundled_item->get_product()->get_id(),
					'quantity'   => $bundle_sell_quantity
				);
			}
		}

		return $bundle_sells_add_to_cart_configuration;
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Validates add-to-cart for bundle-sells.
	 *
	 * @param  boolean  $add
	 * @param  int      $product_id
	 * @param  int      $quantity
	 * @param  mixed    $variation_id
	 * @param  array    $variations
	 * @param  array    $cart_item_data
	 * @return boolean
	 */
	public static function validate_add_to_cart( $add, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		if ( $add ) {

			$product         = wc_get_product( $product_id );
			$bundle_sell_ids = WC_PB_BS_Product::get_bundle_sell_ids( $product );

			if ( ! empty( $bundle_sell_ids ) ) {

				// Construct a dummy bundle to validate the posted form content.
				$bundle = WC_PB_BS_Product::get_bundle( $bundle_sell_ids, $product );

				if ( ( $bundle instanceof WC_Product_Bundle ) && false === WC_PB()->cart->validate_bundle_add_to_cart( $bundle, $quantity, $cart_item_data ) ) {
					$add = false;
				}
			}
		}

		return $add;
	}

	/**
	 * Adds bundle-sells to the cart on the 'woocommerce_add_to_cart' action.
	 * Important: This must run before WooCommerce sets cart session data on 'woocommerce_add_to_cart' (20).
	 *
	 * @param  string  $parent_cart_item_key
	 * @param  int     $parent_id
	 * @param  int     $parent_quantity
	 * @param  int     $variation_id
	 * @param  array   $variation
	 * @param  array   $cart_item_data
	 * @return void
	 */
	public static function bundle_sells_add_to_cart( $parent_cart_item_key, $parent_id, $parent_quantity, $variation_id, $variation, $cart_item_data ) {

		// Only proceed if the product was added to the cart via a form or query string.
		if ( empty( $_REQUEST[ 'add-to-cart' ] ) || absint( $_REQUEST[ 'add-to-cart' ] ) !== absint( $parent_id ) ) {
			return;
		}

		$product = $variation_id > 0 ? wc_get_product( $parent_id ) : WC()->cart->cart_contents[ $parent_cart_item_key ][ 'data' ];

		$bundle_sells_configuration = self::get_posted_bundle_sells_configuration( $product );

		if ( ! empty( $bundle_sells_configuration ) ) {
			foreach ( $bundle_sells_configuration as $bundle_sell_configuration ) {

				// Unique way to identify bundle-sells in the cart.
				$bundle_sell_cart_data = array( 'bundle_sell_of' => $parent_cart_item_key );

				// Add the bundle-sell to the cart.
				$bundle_sell_cart_item_key = WC()->cart->add_to_cart( $bundle_sell_configuration[ 'product_id' ], $bundle_sell_configuration[ 'quantity' ], '', '', $bundle_sell_cart_data );

				// Add a reference in the parent cart item.
				if ( isset( WC()->cart->cart_contents[ $parent_cart_item_key ] ) ) {
					if ( ! isset( WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ] ) ) {
						WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ] = array( $bundle_sell_cart_item_key );
					} else {
						WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ][] = $bundle_sell_cart_item_key;
					}
				}
			}
		}
	}

	/**
	 * Filter the add-to-cart success message to include bundle-sells.
	 *
	 * @param  string  $message
	 * @param  array   $products
	 * @return string
	 */
	public static function bundle_sells_add_to_cart_message_html( $message, $products ) {

		if ( isset( self::$bypass_filters[ 'add_to_cart_message_html' ] ) && self::$bypass_filters[ 'add_to_cart_message_html' ] === 1 ) {
			return $message;
		}

		$parent_product_ids = array_keys( $products );
		$parent_product_id  = current( $parent_product_ids );

		$bundle_sells_configuration = self::get_posted_bundle_sells_configuration( $parent_product_id );

		if ( ! empty( $bundle_sells_configuration ) ) {

			foreach ( $bundle_sells_configuration as $bundle_sell_configuration ) {
				$products[ $bundle_sell_configuration[ 'product_id' ] ] = $bundle_sell_configuration[ 'quantity' ];
			}

			self::$bypass_filters[ 'add_to_cart_message_html' ] = 1;
			$message = wc_add_to_cart_message( $products, true );
			self::$bypass_filters[ 'add_to_cart_message_html' ] = 0;
		}

		return $message;
	}

	/**
	 * Allow bundle-sell discounts to be applied by PB.
	 *
	 * @since  6.0.0
	 *
	 * @param  array  $cart
	 * @return array
	 */
	public static function load_bundle_sells_from_session( $cart ) {

		if ( empty( $cart->cart_contents ) ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			$parent_item = wc_pb_get_bundle_sell_cart_item_container( $cart_item );

			if ( empty( $parent_item ) ) {
				continue;
			}

			$product = $parent_item[ 'data' ];

			if ( $product->is_type( 'variation' ) ) {
				$product = wc_get_product( $parent_item[ 'data' ]->get_parent_id() );
			}

			$bundle_sell_ids = WC_PB_BS_Product::get_bundle_sell_ids( $product );

			if ( empty( $bundle_sell_ids ) ) {
				continue;
			}

			$bundle             = WC_PB_BS_Product::get_bundle( $bundle_sell_ids, $product );
			$bundled_data_items = $bundle->get_bundled_data_items();
			$bundled_item       = null;

			foreach ( $bundled_data_items as $bundled_data_item ) {
				if ( $bundled_data_item->get_product_id() === $cart_item[ 'product_id' ] ) {
					$bundled_item = $bundle->get_bundled_item( $bundled_data_item );
					break;
				}
			}

			if ( $bundled_item ) {
				$cart_item[ 'data' ]->bundled_cart_item = $bundled_item;
			}
		}
	}
}

WC_PB_BS_Cart::init();
