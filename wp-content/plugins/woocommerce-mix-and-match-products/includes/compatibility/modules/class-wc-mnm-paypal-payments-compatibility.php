<?php
/**
 * PayPal Payments Gateway Compatibility
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_NYP_PayPal_Payments_Compatibility class
 **/
class WC_MNM_PayPal_Payments_Compatibility {


	/**
	 * WC_MNM_PayPal_Payments_Compatibility Constructor
	 *
	 * @since 3.0.0
	 */
	public static function init() {
		add_action( 'woocommerce_paypal_payments_product_supports_payment_request_button', array( __CLASS__, 'hide_request_buttons' ), 10, 2 );
	}


	/**
	 * Hide PayPal's payment request buttons
	 *
	 * @param   bool $supports
	 * @param   obj WC_Product
	 * @return  bool
	 */
	public static function hide_request_buttons( $supports, $product ) {

		if ( wc_mnm_is_product_container_type( $product ) ) {
			$supports = false;

		}
		return $supports;
	}
} // End class: do not remove or there will be no more guacamole for you.

WC_MNM_PayPal_Payments_Compatibility::init();
