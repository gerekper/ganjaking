<?php
/**
 * WC_PB_BS_Cart class
 *
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
 * @version  6.12.0
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
			add_filter( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'load_bundle_sells_into_session' ), 10 );
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
				// Add the bundle-sell to the cart.
				$bundle_sell_cart_item_key = WC()->cart->add_to_cart( $bundle_sell_configuration[ 'product_id' ], $bundle_sell_configuration[ 'quantity' ] );
			}
		}

		self::load_bundle_sells_into_session( WC()->cart );
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
	public static function load_bundle_sells_into_session( $cart ) {

		if ( empty( $cart->cart_contents ) ) {
			return;
		}

		$bundle_sells_by_id       = array();
		$cart_item_parent_product = array();
		$search_cart_item_keys    = array();
		$apply_to_cart_item_keys  = array();

		// Identify items to search for bundle-sells and items to apply bundle sells to.
		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			// Bundle containers cannot grant discounts.
			if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
				continue;
			}

			$search_cart_item_keys[] = $cart_item_key;

			// Only Simple products and Simple Subscriptions can receive discounts.
			if ( ! $cart_item[ 'data' ]->is_type( array( 'simple', 'subscription' ) ) ) {
				continue;
			}

			// Bundles and bundled items cannot receive discounts.
			if ( wc_pb_maybe_is_bundled_cart_item( $cart_item ) || wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
				continue;
			}

			// Composites and composited items cannot receive discounts.
			if ( function_exists( 'wc_cp_maybe_is_composited_cart_item' ) && function_exists( 'wc_cp_is_composite_container_cart_item' ) && ( wc_cp_maybe_is_composited_cart_item( $cart_item ) || wc_cp_is_composite_container_cart_item( $cart_item ) ) ) {
				continue;
			}

			$apply_to_cart_item_keys[] = $cart_item_key;
		}

		/**
		 * 'woocommerce_bundle_sells_search_cart_items' filter.
		 *
		 * @since  6.6.0
		 *
		 * @param  array   $cart_item_keys
		 * @param  string  $parent_item
		 * @param  array   $parent_item_name
		 */
		$search_cart_item_keys = apply_filters( 'woocommerce_bundle_sells_search_cart_items', $search_cart_item_keys );

		/**
		 * 'woocommerce_bundle_sells_apply_to_cart_items' filter.
		 *
		 * @since  6.6.0
		 *
		 * @param  bool    $cart_item
		 * @param  string  $parent_item
		 * @param  array  $parent_item_name
		 */
		$apply_to_cart_item_keys = apply_filters( 'woocommerce_bundle_sells_apply_to_cart_items', $apply_to_cart_item_keys );

		// Identify potential bundle-sells, keeping associations to parents with highest discounts.
		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( ! in_array( $cart_item_key, $search_cart_item_keys ) ) {
				continue;
			}

			$product = $cart_item[ 'data' ];

			if ( $product->is_type( 'variation' ) ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			$cart_item_bundle_sells          = WC_PB_BS_Product::get_bundle_sell_ids( $product );
			$cart_item_bundle_sells_discount = WC_PB_BS_Product::get_bundle_sells_discount( $product );

			if ( ! empty( $cart_item_bundle_sells ) ) {

				$cart_item_parent_product[ $cart_item_key ] = $product;

				foreach ( $cart_item_bundle_sells as $bundle_sell_id ) {

					if ( ! isset( $bundle_sells_by_id[ $bundle_sell_id ] ) ) {

						$bundle_sells_by_id[ $bundle_sell_id ] = array(
							'parent_key' => $cart_item_key,
							'discount'   => $cart_item_bundle_sells_discount
						);

					// Keep the highest discount.
					} elseif ( $cart_item_bundle_sells_discount > $bundle_sells_by_id[ $bundle_sell_id ][ 'discount' ] ) {

						$bundle_sells_by_id[ $bundle_sell_id ] = array(
							'parent_key' => $cart_item_key,
							'discount'   => $cart_item_bundle_sells_discount
						);
					}
				}
			}

			// Clean up keys.
			if ( isset( $cart_item[ 'bundle_sells' ] ) ) {
				unset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundle_sells' ] );
			}
			if ( isset( $cart_item[ 'bundle_sell_of' ] ) ) {
				unset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundle_sell_of' ] );
			}
			if ( isset( $cart_item[ 'bundle_sell_discount' ] ) ) {
				unset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundle_sell_discount' ] );
			}
		}

		if ( empty( $bundle_sells_by_id ) ) {
			return;
		}

		// Scan cart for bundle-sells and apply cart item data and associations.
		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( ! in_array( $cart_item_key, $apply_to_cart_item_keys ) ) {
				continue;
			}

			// Found a new bundle-sell?
			if ( isset( $bundle_sells_by_id[ $cart_item[ 'product_id' ] ] ) ) {

				$parent_cart_item_key = $bundle_sells_by_id[ $cart_item[ 'product_id' ] ][ 'parent_key' ];

				WC()->cart->cart_contents[ $cart_item_key ][ 'bundle_sell_of' ] = $parent_cart_item_key;

				if ( $bundle_sells_by_id[ $cart_item[ 'product_id' ] ][ 'discount' ] ) {
					WC()->cart->cart_contents[ $cart_item_key ][ 'bundle_sell_discount' ] = $bundle_sells_by_id[ $cart_item[ 'product_id' ] ][ 'discount' ];
				}

				if ( ! isset( WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ] ) ) {
					WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ] = array( $cart_item_key );
				} elseif ( ! in_array( $cart_item_key, WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ] ) ) {
					WC()->cart->cart_contents[ $parent_cart_item_key ][ 'bundle_sells' ][] = $cart_item_key;
				}
			}
		}

		// Apply bundle-sell discounts.
		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( ! $parent_item_key = wc_pb_get_bundle_sell_cart_item_container( $cart_item, false, true ) ) {
				continue;
			}

			if ( empty( $cart_item_parent_product[ $parent_item_key ] ) ) {
				continue;
			}

			$bundle        = WC_PB_BS_Product::get_bundle( array( $cart_item[ 'product_id' ] ), $cart_item_parent_product[ $parent_item_key ] );
			$bundled_items = $bundle->get_bundled_items();
			$bundled_item  = ! empty( $bundled_items ) ? current( $bundled_items ) : false;

			if ( $bundled_item ) {

				if ( 'filters' === WC_PB_Product_Prices::get_bundled_cart_item_discount_method() ) {
					$cart_item[ 'data' ]->bundled_cart_item = $bundled_item;
				}
			}
		}
	}
}

WC_PB_BS_Cart::init();
