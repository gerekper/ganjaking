<?php
/**
 * Init class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Frontend_Extended' ) ) {
	/**
	 * Frontend class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Frontend_Extended extends YITH_WCWL_Frontend {

		/**
		 * Return localize array
		 *
		 * @return array Array with variables to be localized inside js
		 * @since 2.2.3
		 */
		public function get_localize() {
			$localize = parent::get_localize();

			$localize['actions']['update_item_quantity']     = 'update_item_quantity';
			$localize['nonce']['update_item_quantity_nonce'] = wp_create_nonce( 'update_item_quantity' );

			return $localize;
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Frontend class
 *
 * @return \YITH_WCWL_Frontend
 */
function YITH_WCWL_Frontend_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Frontend_Extended::get_instance();
}
