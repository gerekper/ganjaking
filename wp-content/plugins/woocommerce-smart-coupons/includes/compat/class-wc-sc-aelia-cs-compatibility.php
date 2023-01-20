<?php
/**
 * Compatibility file for WooCommerce Aelia Currency Switcher
 *
 * @author      StoreApps
 * @since       6.1.0
 * @version     1.0.1
 *
 * @package     woocommerce-smart-coupons/includes/compat/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Aelia_CS_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Aelia Currency Switcher
	 */
	class WC_SC_Aelia_CS_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_Aelia_CS_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'wc_aelia_cs_coupon_types_to_convert', array( $this, 'add_smart_coupon' ) );
		}

		/**
		 * Get single instance of WC_SC_Aelia_CS_Compatibility
		 *
		 * @return WC_SC_Aelia_CS_Compatibility Singleton object of WC_SC_Aelia_CS_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add discount type 'smart_coupon' in Aelia Currency Switcher's framework
		 *
		 * @param array $coupon_types Existing coupon types.
		 * @return array $coupon_types
		 */
		public function add_smart_coupon( $coupon_types = array() ) {
			if ( empty( $coupon_types ) || ! is_array( $coupon_types ) ) {
				return $coupon_types;
			}
			if ( ! in_array( 'smart_coupon', $coupon_types, true ) ) {
				$coupon_types[] = 'smart_coupon';
			}
			return $coupon_types;
		}

		/**
		 * Check & convert price
		 *
		 * @param float  $price The price need to be converted.
		 * @param string $to_currency The price will be converted to this currency.
		 * @param string $from_currency The price will be converted from this currency.
		 * @return float
		 */
		public function convert_price( $price = 0, $to_currency = null, $from_currency = null ) {
			if ( empty( $from_currency ) ) {
				$from_currency = get_option( 'woocommerce_currency' ); // Shop base currency.
			}
			if ( empty( $to_currency ) ) {
				$to_currency = get_woocommerce_currency(); // Active currency.
			}
			return apply_filters( 'wc_aelia_cs_convert', $price, $from_currency, $to_currency );
		}

	}

}

WC_SC_Aelia_CS_Compatibility::get_instance();
