<?php
/**
 * Compatibility class with Bundle Products
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.3.1
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

if ( ! class_exists( 'YITH_WCDP_YITH_Products_Bundle' ) ) {
	/**
	 * WooCommerce Composite Products
	 *
	 * @since 1.3.1
	 */
	class YITH_WCDP_YITH_Products_Bundle {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Products_Bundle
		 * @since 1.1.3
		 */
		protected static $_instance;

		/**
		 * Register current bundle product id
		 *
		 * @var int Product ID
		 */
		protected $_bundle;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_YITH_Products_Bundle
		 * @since 1.3.1
		 */
		public function __construct() {
			add_filter( 'yith_wcpb_ajax_get_bundle_total_price_response', array( $this, 'add_deposit_value' ), 10, 2 );
			add_action( 'yith_wcdp_yith_bundle_add_to_cart', array( $this, 'add_single_add_deposit_to_cart' ), 10, 1 );

			add_filter( 'yith_wcdp_skip_cart_item_processing', array(
				$this,
				'remove_deposit_for_bundle_cart_item'
			), 10, 2 );
			add_filter( 'yith_wcdp_skip_cart_item_data_processing', array(
				$this,
				'remove_deposit_for_bundle_cart_item_data'
			), 10, 3 );
			add_filter( 'yith_wcpb_woocommerce_cart_item_price', array( $this, 'filter_bundle_subtotal' ), 10, 3 );
			add_filter( 'yith_wcpb_bundle_pip_bundled_items_subtotal', array(
				$this,
				'filter_bundle_subtotal'
			), 10, 3 );

			add_action( 'yith_wcpb_before_bundle_woocommerce_add_to_cart', array(
				$this,
				'set_deposit_filters_for_bundle'
			), 10, 2 );
			add_action( 'yith_wcpb_after_bundle_woocommerce_add_to_cart', array(
				$this,
				'remove_deposit_filters_for_bundle'
			), 10 );

			add_action( 'yith_wcdp_before_add_to_support_cart', array( $this, 'remove_bundle_handling' ) );
			add_action( 'yith_wcdp_after_add_to_support_cart', array( $this, 'restore_bundle_handling' ) );
		}

		/**
		 * Add deposit value to fragments used by Bundle plugin
		 *
		 * @param $response mixed Fragments used by bundle
		 * @param $product  \WC_Product Current product
		 *
		 * @return mixed Filtered array of fragments
		 */
		public function add_deposit_value( $response, $product ) {
			if ( ! empty( $product->per_items_pricing ) ) {
				$response['deposit_html'] = wc_price( YITH_WCDP_Premium()->get_deposit( $product->get_id(), $response['price'] ) );
			}

			return $response;
		}

		/**
		 * Add "Deposit Form" to Bundle product page
		 *
		 * @param $product \WC_Product Current product
		 *
		 * @return void
		 */
		public function add_single_add_deposit_to_cart( $product ) {
			add_action( 'woocommerce_before_add_to_cart_button', array(
				YITH_WCDP_Frontend_Premium(),
				'print_single_add_deposit_to_cart_template'
			) );
		}

		/**
		 * Remove processing for cart item
		 *
		 * @param $skip      bool Whether to skip process or not
		 * @param $cart_item array Cart item
		 *
		 * @return bool Whether to skip process or not
		 */
		public function remove_deposit_for_bundle_cart_item( $skip, $cart_item ) {
			if ( isset( $cart_item['cartstamp'] ) ) {
				$product = $cart_item['data'];

				if ( ! empty( $product->per_items_pricing ) ) {
					return true;
				}
			}

			return $skip;
		}

		/**
		 * Remove processing for cart item data
		 *
		 * @param $skip           bool Whether to skip process or not
		 * @param $cart_item_data array Cart item data
		 * @param $product        \WC_Product Current product
		 *
		 * @return bool Whether to skip process or not
		 */
		public function remove_deposit_for_bundle_cart_item_data( $skip, $cart_item_data, $product ) {
			if ( isset( $cart_item_data['cartstamp'] ) && ! empty( $product->per_items_pricing ) ) {
				return true;
			}

			return $skip;
		}

		/**
		 * Filter bundle subtotal
		 *
		 * @param $price float Price
		 * @param $arg1  mixed Cart item / Bundle price
		 * @param $arg2  mixed Bundle price / Cart item
		 *
		 * @return float Filtered bundle price
		 */
		public function filter_bundle_subtotal( $price, $arg1, $arg2 ) {
			if ( doing_filter( 'yith_wcpb_woocommerce_cart_item_price' ) ) {
				$cart_item    = $arg2;
				$bundle_price = $arg1;
			} else {
				$cart_item    = $arg1;
				$bundle_price = $arg2;
			}

			$product = $cart_item['data'];

			if ( YITH_WCDP_Premium()->is_deposit_enabled_on_product( $product ) ) {
				$price = wc_price( YITH_WCDP_Premium()->get_deposit( $product->get_id(), $bundle_price ) );
			}

			return $price;
		}

		/**
		 * Set filters for deposits, in order to use bundle instead of bundled items
		 *
		 * @param $cart_item_key string Cart item key
		 * @param $bundle_id     int Bundle id
		 */
		public function set_deposit_filters_for_bundle( $cart_item_key, $bundle_id ) {
			$this->_bundle = $bundle_id;

			add_filter( 'yith_wcdp_is_deposit_enabled_on_product', array(
				$this,
				'is_deposit_enabled_on_bundle'
			), 10, 2 );
			add_filter( 'yith_wcdp_is_deposit_mandatory', array( $this, 'is_deposit_mandatory_for_bundle' ), 10, 2 );
			add_filter( 'yith_wcdp_is_deposit_expired_for_product', array(
				$this,
				'is_deposit_expired_for_bundle'
			), 10, 2 );
			add_filter( 'yith_wcdp_deposit_type', array( $this, 'get_deposit_type_for_bundle' ), 10, 2 );
			add_filter( 'yith_wcdp_deposit_amount', array( $this, 'get_deposit_amount_for_bundle' ), 10, 2 );
			add_filter( 'yith_wcdp_deposit_rate', array( $this, 'get_deposit_rate_for_bundle' ), 10, 2 );
		}

		/**
		 * Filter is_deposit_enabled method to use bundle
		 *
		 * @param $enabled    bool Whether deposit is enabled on bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function is_deposit_enabled_on_bundle( $enabled, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->is_deposit_enabled_on_product( $this->_bundle );
			}

			return $enabled;
		}

		/**
		 * Filter is_deposit_mandatory method to use bundle
		 *
		 * @param $mandatory  bool Whether deposit is mandatory on bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function is_deposit_mandatory_for_bundle( $mandatory, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->is_deposit_mandatory( $this->_bundle );
			}

			return $mandatory;
		}

		/**
		 * Filter is_deposit_expired method to use bundle
		 *
		 * @param $expired    bool Whether deposit is expired on bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function is_deposit_expired_for_bundle( $expired, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->is_deposit_expired_for_product( $this->_bundle );
			}

			return $expired;
		}

		/**
		 * Filter get_deposit_type method to use bundle
		 *
		 * @param $type       string Deposit type for bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function get_deposit_type_for_bundle( $type, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->get_deposit_type( $this->_bundle );
			}

			return $type;
		}

		/**
		 * Filter get_deposit_amount method to use bundle
		 *
		 * @param $amount     float Deposit amount for bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function get_deposit_amount_for_bundle( $amount, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->get_deposit_amount( $this->_bundle );
			}

			return $amount;
		}

		/**
		 * Filter get_deposit_rate method to use bundle
		 *
		 * @param $rate       string Deposit rate for bundle
		 * @param $product_id int Product id
		 *
		 * @return bool Filtered value
		 */
		public function get_deposit_rate_for_bundle( $rate, $product_id ) {
			if ( $product_id != $this->_bundle ) {
				return YITH_WCDP_Premium()->get_deposit_rate( $this->_bundle );
			}

			return $rate;
		}

		/**
		 * Remove all filters previously set for bundle product
		 *
		 * @return void
		 */
		public function remove_deposit_filters_for_bundle() {
			remove_filter( 'yith_wcdp_is_deposit_enabled_on_product', array(
				$this,
				'is_deposit_enabled_on_bundle'
			), 10 );
			remove_filter( 'yith_wcdp_is_deposit_mandatory', array( $this, 'is_deposit_mandatory_for_bundle' ), 10 );
			remove_filter( 'yith_wcdp_is_deposit_expired_for_product', array(
				$this,
				'is_deposit_expired_for_bundle'
			), 10 );
			remove_filter( 'yith_wcdp_deposit_type', array( $this, 'get_deposit_type_for_bundle' ), 10 );
			remove_filter( 'yith_wcdp_deposit_amount', array( $this, 'get_deposit_amount_for_bundle' ), 10 );
			remove_filter( 'yith_wcdp_deposit_rate', array( $this, 'get_deposit_rate_for_bundle' ), 10 );
		}

		/**
		 * Remove bundle handling during support cart processing
		 *
		 * @return void
		 */
		public function remove_bundle_handling() {
			remove_filter( 'woocommerce_add_to_cart', array( YITH_WCPB_Frontend(), 'woocommerce_add_to_cart' ) );
		}

		/**
		 * Restore bundle handling during support cart processing
		 *
		 * @return void
		 */
		public function restore_bundle_handling() {
			add_filter( 'woocommerce_add_to_cart', array( YITH_WCPB_Frontend(), 'woocommerce_add_to_cart' ) );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Products_Bundle
		 * @since 1.3.1
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
 * Unique access to instance of YITH_WCDP_YITH_Products_Bundle class
 *
 * @return \YITH_WCDP_YITH_Composite_Products
 * @since 1.3.1
 */
function YITH_WCDP_YITH_Products_Bundle() {
	return YITH_WCDP_YITH_Products_Bundle::get_instance();
}

