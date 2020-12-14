<?php
/**
 * Stripe Gateway Compatibility
 *
 * @author   Kathy Darling
 * @package  WooCommerce Mix and Match/Compatibility
 * @since    1.10.6
 * @version  1.10.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_MNM_Stripe_Compatibility class
 **/
class WC_MNM_Stripe_Compatibility {


	/**
	 * WC_MNM_Stripe_Compatibility Constructor
	 */
	public static function init() {

		// Hide Stripe's payment request buttons on MNNM products.
		add_filter( 'wc_stripe_payment_request_supported_types', array( __CLASS__, 'supported_request_product_types' ) );

	}

	/**
	 * Hide Stripe's instant pay buttons
	 *
	 * @param   array $types - The product types that can support payment request buttons.
	 * @return  array
	 */
	public static function supported_request_product_types( $types ) {
		$key = array_search( 'mix-and-match', $types );

		if ( $key ) { 
			unset( $types[$key] );
		}
		
		return $types;
	}


} // End class: do not remove or there will be no more guacamole for you.

WC_MNM_Stripe_Compatibility::init();
