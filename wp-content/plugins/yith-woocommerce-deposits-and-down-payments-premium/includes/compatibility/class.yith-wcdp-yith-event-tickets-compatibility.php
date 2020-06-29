<?php
/**
 * Compatibility class with Event Tickets
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

if ( ! class_exists( 'YITH_WCDP_YITH_Event_Tickets' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_YITH_Event_Tickets {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Event_Tickets
		 * @since 1.0.5
		 */
		protected static $_instance;

		public function __construct() {
			add_action( 'yith_wcdp_ticket-event_add_to_cart', array(
				$this,
				'print_single_add_deposit_to_cart_template'
			) );
			add_action( 'enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 *
		 */
		public function print_single_add_deposit_to_cart_template() {
			add_action( 'woocommerce_before_add_to_cart_button', array(
				YITH_WCDP_Frontend_Premium(),
				'print_single_add_deposit_to_cart_template'
			) );
		}

		/**
		 *
		 */
		public function enqueue_scripts() {

		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Event_Tickets
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
 * Unique access to instance of YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts class
 *
 * @return \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
 * @since 1.0.0
 */
function YITH_WCDP_YITH_Event_Tickets() {
	return YITH_WCDP_YITH_Event_Tickets::get_instance();
}