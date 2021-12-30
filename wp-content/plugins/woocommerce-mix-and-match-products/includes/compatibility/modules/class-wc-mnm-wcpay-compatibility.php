<?php
/**
 * WooCommerce Payments Gateway Compatibility
 *
 * @author   Kathy Darling
 * @package  WooCommerce Mix and Match/Compatibility
 * @since    1.11.6
 * @version  1.11.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_MNM_WCPay_Compatibility class
 **/
class WC_MNM_WCPay_Compatibility {

	/**
	 * WC_MNM_WCPay_Compatibility Constructor
	 */
	public static function init() {
		add_filter( 'wcpay_payment_request_is_product_supported', array( __CLASS__, 'hide_request_buttons' ), 10, 2 );
	}
   
	/**
	 * Hide payment request pay buttons
	 *
	 * @param   bool        $supported
	 * @param   WC_Product  $product
	 * @return  bool
	 */
	public static function hide_request_buttons( $supported, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$supported = false;
		}
		return $supported;
	}


} // End class: do not remove or there will be no more guacamole for you.

WC_MNM_WCPay_Compatibility::init();
