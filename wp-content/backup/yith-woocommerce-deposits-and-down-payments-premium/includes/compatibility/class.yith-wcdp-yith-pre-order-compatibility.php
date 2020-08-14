<?php
/**
 * Compatibility class with Pre Order
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

if ( ! class_exists( 'YITH_WCDP_YITH_Pre_Order' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_YITH_Pre_Order {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Pre_Order
		 * @since 1.0.5
		 */
		protected static $_instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_YITH_Pre_Order
		 * @since 1.0.5
		 */
		public function __construct() {
			add_filter( 'yith_wcpo_return_original_price', array( $this, 'return_deposit_price' ), 10, 2 );
		}

		/**
		 * Tells Pre Order to return original product price, if deposit is applied
		 *
		 * @return bool
		 * @since 1.0.5
		 */
		public function return_deposit_price( $return_original_price, $product ) {
			if ( yit_get_prop( $product, 'yith_wcdp_deposit' ) || yit_get_prop( $product, 'yith_wcdp_balance' ) ) {
				$return_original_price = true;
			}

			return $return_original_price;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Pre_Order
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
 * Unique access to instance of YITH_WCDP_YITH_Pre_Order class
 *
 * @return \YITH_WCDP_YITH_Pre_Order
 * @since 1.0.0
 */
function YITH_WCDP_YITH_Pre_Order() {
	return YITH_WCDP_YITH_Pre_Order::get_instance();
}

