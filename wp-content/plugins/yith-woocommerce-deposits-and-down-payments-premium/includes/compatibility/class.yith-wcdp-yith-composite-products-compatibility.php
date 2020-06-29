<?php
/**
 * Compatibility class with Composite Products
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.1.3
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

if ( ! class_exists( 'YITH_WCDP_YITH_Composite_Products' ) ) {
	/**
	 * WooCommerce Composite Products
	 *
	 * @since 1.1.3
	 */
	class YITH_WCDP_YITH_Composite_Products {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Composite_Products
		 * @since 1.1.3
		 */
		protected static $_instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_YITH_Composite_Products
		 * @since 1.1.3
		 */
		public function __construct() {
			add_action( "yith_wcdp_yith-composite_add_to_cart", array( $this, 'add_deposit_on_composite' ), 10, 1 );
			add_filter( 'yith_wcp_composite_children_subtotal', array(
				$this,
				'update_composite_children_subtotal'
			), 10, 4 );

			// change checkout process
			add_filter( 'woocommerce_add_cart_item', array( $this, 'update_cart_item' ), 25, 3 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'update_cart_item_data' ), 25, 3 );

			// fix for support cart
			add_action( 'yith_wcdp_before_add_to_support_cart', array( $this, 'adding_to_support_cart' ) );
			add_action( 'yith_wcdp_after_add_to_support_cart', array( $this, 'added_to_support_cart' ) );

			add_filter( 'woocommerce_product_needs_shipping', array( $this, 'check_composite_shipping' ), 99, 2 );

		}

		/**
		 * Add deposit options for Composite Prodducts
		 *
		 * @param $product \WC_Product Current product
		 *
		 * @return void
		 *
		 * @since 1.1.3
		 */
		public function add_deposit_on_composite( $product ) {
			add_action( 'woocommerce_before_add_to_cart_button', array(
				YITH_WCDP_Frontend_Premium(),
				'print_single_add_deposit_to_cart_template'
			) );
		}

		/**
		 * Filters components total as it was calculated by YITH WooCommerce Composite Products, and changes it to its deposit value
		 * Deposit value is calculated using deposit options from Composite product
		 *
		 * @param $subtotal       float Current components subtotal
		 * @param $product        \WC_Product_Yith_Composite Composite product
		 * @param $component_data array Component for current cart item
		 * @param $cart_item_key  string Current cart item key
		 *
		 * @return float Filtered components subtotal
		 *
		 * @since 1.1.3
		 */
		public function update_composite_children_subtotal( $subtotal, $product, $component_data, $cart_item_key ) {
			$cart_contents = WC()->cart->cart_contents;

			if ( isset( $cart_contents[ $cart_item_key ] ) && isset( $cart_contents[ $cart_item_key ]['deposit'] ) && $cart_contents[ $cart_item_key ]['deposit'] ) {
				$subtotal_deposit = min( YITH_WCDP_Premium()->get_deposit( yit_get_product_id( $product ), $subtotal ), $subtotal );

				$subtotal = $subtotal_deposit;
			}

			return $subtotal;
		}

		/* === CHECKOUT PROCESS METHODS === */

		/**
		 * Update cart item when deposit is selected
		 *
		 * @param $cart_item mixed Current cart item
		 *
		 * @return mixed Filtered cart item
		 * @since 1.0.0
		 */
		public function update_cart_item( $cart_item ) {

			$composite_parent = isset( $cart_item['yith_wcp_child_component_data'] ) ? $cart_item['yith_wcp_child_component_data'] : false;

			if ( ! $composite_parent ) {
				return $cart_item;
			}

			/**
			 * @var $product          \WC_Product
			 * @var $original_product \WC_Product
			 */
			$product          = $cart_item['yith_wcp_child_component_data']['yith_wcp_component_parent_object'];
			$original_product = $cart_item['data'];

			$product_id   = $product->get_id();
			$variation_id = false;

			if ( YITH_WCDP_Premium()->is_deposit_enabled_on_product( $product_id, $variation_id ) && ! yit_get_prop( $product, 'yith_wcdp_deposit', true ) && ! apply_filters( 'yith_wcdp_skip_cart_item_processing', false, $cart_item ) ) {
				$deposit_forced = YITH_WCDP_Premium()->is_deposit_mandatory( $product_id, $variation_id );

				$deposit_value   = apply_filters( 'yith_wcdp_deposit_value', YITH_WCDP_Premium()->get_deposit( $product_id, $original_product->get_price(), 'edit', false, $variation_id ), $product_id, $variation_id, $cart_item );
				$deposit_balance = apply_filters( 'yith_wcdp_deposit_balance', max( $original_product->get_price() - $deposit_value, 0 ), $product_id, $variation_id, $cart_item );

				if (
					apply_filters( 'yith_wcdp_process_cart_item_product_change', true, $cart_item ) &&
					isset( $_REQUEST['add-to-cart'] ) &&
					( ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' ) )
				) {
					yit_set_prop( $cart_item['data'], 'price', $deposit_value );
					yit_set_prop( $cart_item['data'], 'yith_wcdp_deposit', true );

					if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
						yit_set_prop( $cart_item['data'], 'virtual', 'yes' );
					}

					$cart_item['deposit_value']   = $deposit_value;
					$cart_item['deposit_balance'] = $deposit_balance;
				}
			}

			return $cart_item;
		}

		/**
		 * Add cart item data when deposit is selected, to store info to save with order
		 *
		 * @param $cart_item_data mixed Currently saved cart item data
		 * @param $product_id     int   Product id
		 *
		 * @return mixed Filtered cart item data
		 * @since 1.0.0
		 */
		public function update_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			$composite_parent = isset( $cart_item_data['yith_wcp_child_component_data'] ) ? $cart_item_data['yith_wcp_child_component_data'] : false;

			if ( ! $composite_parent ) {
				return $cart_item_data;
			}

			$product          = $cart_item_data['yith_wcp_child_component_data']['yith_wcp_component_parent_object'];
			$original_product = wc_get_product( ! empty( $variation_id ) ? $variation_id : $product_id );

			$product_id   = yit_get_product_id( $product );
			$variation_id = false;

			if ( YITH_WCDP_Premium()->is_deposit_enabled_on_product( $product_id, $variation_id ) && ! apply_filters( 'yith_wcdp_skip_cart_item_data_processing', false, $cart_item_data, $product ) ) {
				$deposit_forced = YITH_WCDP_Premium()->is_deposit_mandatory( $product_id, $variation_id );

				$deposit_type   = YITH_WCDP_Premium()->get_deposit_type( $product_id, false, $variation_id );
				$deposit_amount = YITH_WCDP_Premium()->get_deposit_amount( $product_id, false, $variation_id );
				$deposit_rate   = YITH_WCDP_Premium()->get_deposit_rate( $product_id, false, $variation_id );

				$deposit_value   = YITH_WCDP_Premium()->get_deposit( $product_id, $original_product->get_price(), 'edit', false, $variation_id );
				$deposit_balance = max( $product->get_price() - $deposit_value, 0 );

				$process_deposit = ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' );

				if ( apply_filters( 'yith_wcdp_process_deposit', $process_deposit, $cart_item_data ) ) {
					$cart_item_data['deposit']                 = true;
					$cart_item_data['deposit_type']            = $deposit_type;
					$cart_item_data['deposit_amount']          = $deposit_amount;
					$cart_item_data['deposit_rate']            = $deposit_rate;
					$cart_item_data['deposit_value']           = $deposit_value;
					$cart_item_data['deposit_balance']         = $deposit_balance;
					$cart_item_data['deposit_shipping_method'] = isset( $_POST['shipping_method'] ) ? $_POST['shipping_method'] : false;
				}
			}

			return $cart_item_data;
		}

		/**
		 * Removes Composite child handling when adding a product to support cart
		 *
		 * @eturn void
		 */
		public function adding_to_support_cart() {
			add_filter( 'yith_wcp_composite_add_child_items', '__return_false' );
		}

		/**
		 * Enables again Composite child handling after adding a product to support cart
		 *
		 * @eturn void
		 */

		public function added_to_support_cart() {
			remove_filter( 'yith_wcp_composite_add_child_items', '__return_false' );
		}


		public function check_composite_shipping( $needs_shipping, $product ) {
			if ( $product->is_type( 'yith-composite' ) && apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
				$needs_shipping = false;
			}

			return $needs_shipping;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
		 * @since 1.1.3
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


	}
}

/**
 * Unique access to instance of YITH_WCDP_YITH_Composite_Products class
 *
 * @return \YITH_WCDP_YITH_Composite_Products
 * @since 1.1.3
 */
function YITH_WCDP_YITH_Composite_Products() {
	return YITH_WCDP_YITH_Composite_Products::get_instance();
}

