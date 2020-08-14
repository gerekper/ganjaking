<?php
/**
 * Compatibility class with Advanced Product Options
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

if ( ! class_exists( 'YITH_WCDP_YITH_Advanced_Product_Options' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_YITH_Advanced_Product_Options {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Advanced_Product_Options
		 * @since 1.0.5
		 */
		protected static $_instance;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCDP_YITH_Advanced_Product_Options
		 */
		public function __construct() {
			add_filter( 'yith_wcdp_deposit_value', array( $this, 'fix_sold_individually_addons' ), 10, 4 );
			add_action( 'yith_wcdp_before_add_to_support_cart', array( $this, 'remove_sold_individually' ) );
			add_action( 'yith_wcdp_after_add_to_support_cart', array( $this, 'add_sold_individually' ) );
		}

		/**
		 * Change deposit value for sold individually add-ons when deposit is a fixed amount
		 *
		 * @param $deposit_value float Deposit value
		 * @param $product_id    int Product ID
		 * @param $variation_id  int Variation ID
		 * @param $cart_item     array Cart item
		 *
		 * @return float Filtered deposit value
		 */
		public function fix_sold_individually_addons( $deposit_value, $product_id, $variation_id, $cart_item ) {
			if (
				isset( $cart_item['yith_wapo_sold_individually'] ) &&
				$cart_item['yith_wapo_sold_individually'] &&
				'amount' == YITH_WCDP_PREMIUM()->get_deposit_type( $product_id, false, $variation_id )
			) {
				$deposit_value = 0;
			}

			return $deposit_value;
		}

		/**
		 * Remove add_to_cart_sold_individually function before adding to support cart
		 *
		 * @return void
		 */
		public function remove_sold_individually() {
			if ( function_exists( 'YITH_WAPO' ) ) {
				remove_action( 'woocommerce_add_to_cart', array(
					YITH_WAPO()->frontend,
					'add_to_cart_sold_individually'
				), 10 );
			}
		}

		/**
		 * Adds add_to_cart_sold_individually back after adding to support cart
		 *
		 * @return void
		 */
		public function add_sold_individually() {
			if ( function_exists( 'YITH_WAPO' ) ) {
				add_action( 'woocommerce_add_to_cart', array(
					YITH_WAPO()->frontend,
					'add_to_cart_sold_individually'
				), 10, 6 );
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Advanced_Product_Options
		 * @since 1.0.5
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
 * Unique access to instance of YITH_WCDP_YITH_Advanced_Product_Options class
 *
 * @return \YITH_WCDP_YITH_Advanced_Product_Options
 * @since 1.0.0
 */
function YITH_WCDP_YITH_Advanced_Product_Options() {
	return YITH_WCDP_YITH_Advanced_Product_Options::get_instance();
}

