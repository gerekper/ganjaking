<?php
/**
 * Stripe Gateway Compatibility
 *
 * @author   Kathy Darling
 * @package  WooCommerce Mix and Match/Compatibility
 * @since    1.10.6
 * @version  1.11.2
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

        // Add support for MNM products.
        add_filter( 'wc_stripe_payment_request_supported_types', array( __CLASS__, 'supported_request_product_types' ) );

		// Hide Stripe's payment request buttons for MNNM products on single product page.
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', array( __CLASS__, 'hide_payment_request_on_product_page' ), 10, 2 );

	}

	/**
     * Add support for MNM products.
	 *
	 * @param   array $types - The product types that can support payment request buttons.
	 * @return  array
	 */
	public static function supported_request_product_types( $types ) {
		$types[] = 'mix-and-match';	
		return $types;
	}

    /**
	 * Hide Stripe's instant pay buttons
	 *
	 * @param   bool $hide - true if hiding request buttons.
     * @param   obj WP_POST - Global WP post.
	 * @return  bool
	 */
	public static function hide_payment_request_on_product_page( $hide, $post ) {

        if ( $post instanceof WP_POST && 'product' === $post->post_type ) {
            
            $product_type = WC_Product_Factory::get_product_type( $post->ID ); 
            
            if ( 'mix-and-match' === $product_type ) {
                $hide = true;
            }
        } 
        
		return $hide;
	}

} // End class: do not remove or there will be no more guacamole for you.

WC_MNM_Stripe_Compatibility::init();
