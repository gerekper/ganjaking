<?php
/**
 * WooCommerce Compatibility Class
 *
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Chained_Products_WC_Compatibility' ) ) {

	/**
	 * WooCommerce Compatibility Class for Chained Products
	 */
	class Chained_Products_WC_Compatibility {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 2.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_25() {
			return self::is_wc_greater_than( '2.4.13' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 2.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_26() {
			return self::is_wc_greater_than( '2.5.5' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_30() {
			return self::is_wc_greater_than( '2.6.14' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.1
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_31() {
			return self::is_wc_greater_than( '3.0.9' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.2
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_32() {
			return self::is_wc_greater_than( '3.1.2' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.3
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_33() {
			return self::is_wc_greater_than( '3.2.6' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.4
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_34() {
			return self::is_wc_greater_than( '3.3.5' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_35() {
			return self::is_wc_greater_than( '3.4.7' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_36() {
			return self::is_wc_greater_than( '3.5.8' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.7
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_37() {
			return self::is_wc_greater_than( '3.6.5' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.8
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_38() {
			return self::is_wc_greater_than( '3.7.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.9
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_39() {
			return self::is_wc_greater_than( '3.8.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_40() {
			return self::is_wc_greater_than( '3.9.3' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.1
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_41() {
			return self::is_wc_greater_than( '4.0.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.2
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_42() {
			return self::is_wc_greater_than( '4.1.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.3
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_43() {
			return self::is_wc_greater_than( '4.2.2' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.4
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_44() {
			return self::is_wc_greater_than( '4.3.3' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_45() {
			return self::is_wc_greater_than( '4.4.1' );
		}

		/**
		 * WooCommerce Current WooCommerce Version
		 *
		 * @return string woocommerce version
		 */
		public static function get_wc_version() {
			if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
				return WC_VERSION;
			}
			if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
				return WOOCOMMERCE_VERSION;
			}
			return null;
		}

		/**
		 * Compare passed version with woocommerce current version
		 *
		 * @param string $version Version to compare with.
		 * @return boolean
		 */
		public static function is_wc_greater_than( $version ) {
			return version_compare( self::get_wc_version(), $version, '>' );
		}
	}
}
