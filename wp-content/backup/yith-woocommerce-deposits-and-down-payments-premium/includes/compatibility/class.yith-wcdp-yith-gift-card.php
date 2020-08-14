<?php
/**
 * Compatibility class with Gift Cards
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

if ( ! class_exists( 'YITH_WCDP_YITH_Gift_Cards' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_YITH_Gift_Cards {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Gift_Cards
		 * @since 1.0.5
		 */
		protected static $_instance;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCDP_YITH_Gift_Cards
		 */
		public function __construct() {
			add_filter( 'yith_ywgc_apply_gift_card_discount_before_cart_total', array(
				$this,
				'remove_cart_restore_after_gift_card_processing'
			) );
		}

		/**
		 *
		 */
		public function remove_cart_restore_after_gift_card_processing() {
			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				return;
			}

			add_action( 'yith_wcdp_reset_cart_after_suborder_processing', '__return_false' );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Gift_Cards
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

function YITH_WCDP_YITH_Gift_Cards() {
	return YITH_WCDP_YITH_Gift_Cards::get_instance();
}