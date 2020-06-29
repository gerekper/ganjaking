<?php
/**
 * Compatibility class for WooCommerce 2.6
 *
 * @category Class
 * @package WC-compat
 * @author StoreApps
 * @version 1.0.0
 * @since WooCommerce 2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_2_6' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 2.6
	 */
	class SA_WC_Compatibility_2_6 extends SA_WC_Compatibility_2_5 {

		/**
		 * Function to check if WooCommerce version is greater than and equal to 2.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_26() {
			return self::is_wc_greater_than( '2.5.5' );
		}

	}

}
