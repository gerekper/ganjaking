<?php
/**
 * Wishlist Exception class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
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
		private $_errorCodes = array(
			0 => 'error',
			1 => 'exists'
		);

		public function getTextualCode() {
			$code = $this->getCode();

			if( array_key_exists( $code, $this->_errorCodes ) ){
				return $this->_errorCodes[ $code ];
			}

			return 'error';
		}
	}
}