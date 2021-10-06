<?php
/**
 * Compatibility class for WooCommerce 4.4.0
 *
 * @category    Class
 * @package     WC-compat
 * @author      StoreApps
 * @version     1.0.1
 * @since       WooCommerce 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_4_4' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 4.4.0
	 */
	class SA_WC_Compatibility_4_4 extends SA_WC_Compatibility_4_3 {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.4.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_44() {
			return self::is_wc_greater_than( '4.3.3' );
		}

	}

}
