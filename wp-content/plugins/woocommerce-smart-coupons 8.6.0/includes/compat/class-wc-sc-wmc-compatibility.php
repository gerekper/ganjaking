<?php
/**
 * Compatibility file for Woo Multi Currency by VillaTheme https://wordpress.org/plugins/woo-multi-currency/
 *
 * @author      StoreApps
 * @since       4.17.0
 * @version     1.0.0
 *
 * @package     woocommerce-smart-coupons/includes/compat/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_WMC_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with Woo Multi Currency
	 */
	class WC_SC_WMC_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_WMC_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_filter( 'wc_sc_credit_called_price_order', array( $this, 'credit_called_price_order' ), 10, 2 );
			add_filter( 'wc_sc_credit_called_price_cart', array( $this, 'credit_called_price_cart' ), 10, 2 );

		}

		/**
		 * Get single instance of WC_SC_WMC_Compatibility
		 *
		 * @return WC_SC_WMC_Compatibility Singleton object of WC_SC_WMC_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Function to modify the value of the credit called
		 *
		 * @param mized $price The price.
		 * @param array $args Additional arguments.
		 * @return mized
		 */
		public function credit_called_price_order( $price = 0, $args = array() ) {
			if ( function_exists( 'wmc_get_price' ) ) {
				$order    = ( ! empty( $args['order_obj'] ) ) ? $args['order_obj'] : null;
				$currency = ( is_object( $order ) && is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : false;
				$price    = wmc_get_price( $price, $currency );
			}
			return $price;
		}

		/**
		 * Function to modify the value of the credit called
		 *
		 * @param mized $price The price.
		 * @param array $args Additional arguments.
		 * @return mized
		 */
		public function credit_called_price_cart( $price = 0, $args = array() ) {
			if ( function_exists( 'wmc_get_price' ) ) {
				$price = wmc_get_price( $price );
			}
			return $price;
		}

	}

}

WC_SC_WMC_Compatibility::get_instance();
