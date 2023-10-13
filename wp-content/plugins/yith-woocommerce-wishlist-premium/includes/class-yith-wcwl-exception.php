<?php
/**
 * Wishlist Exception class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Exception' ) ) {
	/**
	 * WooCommerce Wishlist Exception
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Exception extends Exception {
		/**
		 * Available error codes
		 *
		 * @var array
		 */
		private $error_codes = array(
			0 => 'error',
			1 => 'exists',
		);

		/**
		 * Returns textual code for the error
		 *
		 * @return string Textual code of the error.
		 */
		public function getTextualCode() {
			$code = $this->getCode();

			if ( array_key_exists( $code, $this->error_codes ) ) {
				return $this->error_codes[ $code ];
			}

			return 'error';
		}
	}
}
