<?php
/**
 * Support Cart for Deposit
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Support_Cart' ) ) {
	/**
	 * Support cart for deposit
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Support_Cart extends WC_Cart {

		/**
		 * Constructor for the cart class. Loads options and hooks in the init method.
		 */
		public function __construct() {
			$this->session          = new YITH_WCDP_Support_Cart_Session();
			$this->fees_api         = new WC_Cart_Fees( $this );
			$this->tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

			add_action( 'woocommerce_add_to_cart', array( $this, 'calculate_totals' ), 20, 0 );
			add_action( 'woocommerce_applied_coupon', array( $this, 'calculate_totals' ), 20, 0 );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'calculate_totals' ), 20, 0 );
			add_action( 'woocommerce_cart_item_restored', array( $this, 'calculate_totals' ), 20, 0 );
			add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ), 1 );
			add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_coupons' ), 1 );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_customer_coupons' ), 1 );
		}

		/**
		 * Populate support cart with some contents
		 *
		 * @param $items mixed Array of cart items
		 *
		 * @return bool
		 */
		public function populate( $items ) {
			foreach ( $items as $cart_item ) {
				$product_id   = $cart_item['product_id'];
				$variation_id = $cart_item['variation_id'];
				$quantity     = $cart_item['quantity'];
				$item_meta    = $cart_item;

				if ( isset( $cart_item['deposit_shipping_method'] ) ) {
					$item_meta['deposit_shipping_method'] = $cart_item['deposit_shipping_method'];
				}

				try {
					$this->add_to_cart( $product_id, $quantity, $variation_id, array(), $item_meta );
				} catch ( Exception $e ) {
					return false;
				}
			}

			if ( apply_filters( 'yith_wcdp_propagate_coupons', false ) ) {
				$this->applied_coupons = WC()->cart->applied_coupons;
			}

			$this->calculate_shipping();
			$this->calculate_totals();

			return true;
		}

		/**
		 * Given a set of packages with rates, get the chosen ones only.
		 *
		 * @param array $calculated_shipping_packages Array of packages.
		 *
		 * @return array
		 * @since 3.2.0
		 */
		public function get_chosen_shipping_methods( $calculated_shipping_packages = array() ) {
			$chosen_methods         = array();
			$original_chosen_method = WC()->session->get( 'chosen_shipping_methods' );

			// Get chosen methods for each package to get our totals.
			foreach ( $calculated_shipping_packages as $key => $package ) {

				if ( isset( $package['chosen_shipping_methods'] ) ) {
					WC()->session->set( 'chosen_shipping_methods', $package['chosen_shipping_methods'] );
				}

				$chosen_method = isset( $package['chosen_shipping_methods'] ) && is_array( $package['chosen_shipping_methods'] ) ? array_pop( $package['chosen_shipping_methods'] ) : false;

				if ( ! $chosen_method || ! isset( $package['rates'][ $chosen_method ] ) ) {
					$chosen_method = wc_get_chosen_shipping_method_for_package( $key, $package );
				}

				if ( $chosen_method ) {
					$chosen_methods[ $key ] = $package['rates'][ $chosen_method ];
				}

				WC()->session->set( 'chosen_shipping_methods', $original_chosen_method );
			}

			return $chosen_methods;
		}

		/**
		 * Get packages to calculate shipping for.
		 *
		 * This lets us calculate costs for carts that are shipped to multiple locations.
		 *
		 * Shipping methods are responsible for looping through these packages.
		 *
		 * By default we pass the cart itself as a package - plugins can change this.
		 * through the filter and break it up.
		 *
		 * @return array of cart items
		 * @since 1.5.4
		 */
		public function get_shipping_packages() {
			$packages = array();

			$items_per_method = array();

			if ( ! empty( $this->cart_contents ) ) {
				foreach ( $this->cart_contents as $cart_item ) {
					if ( ! empty( $cart_item['deposit_shipping_method'] ) ) {
						$method_hash = md5( maybe_serialize( $cart_item['deposit_shipping_method'] ) );
						$method      = $cart_item['deposit_shipping_method'];
					} else {
						$method_hash = md5( maybe_serialize( WC()->session->get( 'chosen_shipping_methods' ) ) );
						$method      = WC()->session->get( 'chosen_shipping_methods' );
					}

					$items_per_method[ $method_hash ]            = isset( $items_per_method[ $method_hash ] ) ? $items_per_method[ $method_hash ] : array(
						'method' => $method,
						'items'  => array()
					);
					$items_per_method[ $method_hash ]['items'][] = $cart_item;
				}
			}

			if ( ! empty( $items_per_method ) ) {
				foreach ( $items_per_method as $data ) {
					$package_subtotal = array_sum( wp_list_pluck( $data['items'], 'line_total' ) );

					$packages[] = array(
						'contents'                => $data['items'],
						'contents_cost'           => $package_subtotal,
						'applied_coupons'         => $this->get_applied_coupons(),
						'user'                    => array(
							'ID' => get_current_user_id(),
						),
						'destination'             => array(
							'country'   => $this->get_customer()->get_shipping_country(),
							'state'     => $this->get_customer()->get_shipping_state(),
							'postcode'  => $this->get_customer()->get_shipping_postcode(),
							'city'      => $this->get_customer()->get_shipping_city(),
							'address'   => $this->get_customer()->get_shipping_address(),
							'address_2' => $this->get_customer()->get_shipping_address_2(),
						),
						'cart_subtotal'           => $this->get_displayed_subtotal(),
						'chosen_shipping_methods' => $data['method']
					);
				}
			}

			return $packages;
		}

		/**
		 * Empty cart (do not affect current user session)
		 *
		 * @param $clear_persistent_cart bool Not used.
		 *
		 * @return void
		 */
		public function empty_cart( $clear_persistent_cart = true ) {
			$this->cart_contents              = array();
			$this->removed_cart_contents      = array();
			$this->shipping_methods           = array();
			$this->coupon_discount_totals     = array();
			$this->coupon_discount_tax_totals = array();
			$this->applied_coupons            = array();
			$this->totals                     = $this->default_totals;

			$this->fees_api->remove_all_fees();
		}

		/**
		 * Add a product to the cart.
		 *
		 * @param int   $product_id     contains the id of the product to add to the cart.
		 * @param int   $quantity       contains the quantity of the item to add.
		 * @param int   $variation_id   ID of the variation being added to the cart.
		 * @param array $variation      attribute values.
		 * @param array $cart_item_data extra cart item data we want to pass into the item.
		 *
		 * @return string|bool $cart_item_key
		 * @throws Exception Plugins can throw an exception to prevent adding to cart.
		 */
		public function add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
			do_action( 'yith_wcdp_before_add_to_support_cart', $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			$cart_item_key = parent::add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			do_action( 'yith_wcdp_after_add_to_support_cart', $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			return $cart_item_key;

		}

		/**
		 * Removes stock handling for items in support cart
		 * (stock level is checked on main cart, this cart just need for additional calculations)
		 *
		 * @return bool
		 */
		public function check_cart_item_stock() {
			return true;
		}
	}
}

if ( ! class_exists( 'YITH_WCDP_Support_Cart_Session' ) ) {
	/**
	 * Support cart session for deposit
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Support_Cart_Session {

		public function set_session() {
			return;
		}

		public function persistent_cart_destroy() {
			return;
		}

		public function get_cart_from_session() {
			return null;
		}

		public function get_cart_for_session() {
			return null;
		}
	}
}