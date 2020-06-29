<?php
/**
 * Compatibility class for WooCommerce 3.9.0
 *
 * @category    Class
 * @package     WC-compat
 * @author      StoreApps
 * @version     1.0.0
 * @since       WooCommerce 3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_3_9' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 3.9.0
	 */
	class SA_WC_Compatibility_3_9 extends SA_WC_Compatibility_3_8 {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.9.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_39() {
			return self::is_wc_greater_than( '3.8.1' );
		}

	}

}
