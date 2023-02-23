<?php
/**
 * Compatibility class for WooCommerce 4.4.0
 *
 * @category    Class
 * @package     WC-compat
 * @author      StoreApps
 * @version     1.1.0
 * @since       WooCommerce 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_4_4' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 4.4.0
	 */
	class SA_WC_Compatibility_4_4 {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.4.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_44() {
			return self::is_wc_greater_than( '4.3.3' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.3.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_43() {
			return self::is_wc_greater_than( '4.2.2' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.2.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_42() {
			return self::is_wc_greater_than( '4.1.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.1.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_41() {
			return self::is_wc_greater_than( '4.0.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.0.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_40() {
			return self::is_wc_greater_than( '3.9.3' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.9.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_39() {
			return self::is_wc_greater_than( '3.8.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.8.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_38() {
			return self::is_wc_greater_than( '3.7.1' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.7.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_37() {
			return self::is_wc_greater_than( '3.6.5' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.6.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_36() {
			return self::is_wc_greater_than( '3.5.8' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.5.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_35() {
			return self::is_wc_greater_than( '3.4.7' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.3.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_34() {
			return self::is_wc_greater_than( '3.3.5' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.3.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_33() {
			return self::is_wc_greater_than( '3.2.6' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.2.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_32() {
			return self::is_wc_greater_than( '3.1.2' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.1.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_31() {
			return self::is_wc_greater_than( '3.0.9' );
		}

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.0.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_30() {
			return self::is_wc_greater_than( '2.6.14' );
		}

		/**
		 * Function to check if WooCommerce version is greater than and equal to 2.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_26() {
			return self::is_wc_greater_than( '2.5.5' );
		}

		/**
		 * Function to check if WooCommerce version is greater than and equal To 2.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_25() {
			return self::is_wc_greater_than( '2.4.13' );
		}

		/**
		 * Function to get WooCommerce version
		 *
		 * @return string version or null.
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
		 * Function to compare current version of WooCommerce on site with active version of WooCommerce
		 *
		 * @param string $version Version number to compare.
		 * @return bool
		 */
		public static function is_wc_greater_than( $version ) {
			return version_compare( self::get_wc_version(), $version, '>' );
		}

	}

}
