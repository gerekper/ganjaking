<?php
/**
 * @package		WooCommerce One Page Checkout
 * @subpackage	Bookings Extension Compatibility
 * @category	Template Class
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class to hold bookings compat functionality until merge with the main extension for dev purposes
 */
class WCOPC_Compat_Bookings {

	public function __construct() {

		add_action( 'wcopc_booking_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_booking' ) );

		// Unhook 'WC_Bookings_Cart::add_to_cart_redirect' from 'add_to_cart_redirect' in OPC pages, to prevent redirection to the default cart when checking booking availability
		if ( isset( $_POST['is_opc'] ) && ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'woocommerce_checkout' ) || ( isset( $_REQUEST['wc-ajax'] ) && 'checkout' == $_REQUEST['wc-ajax'] ) ) ) {
			remove_action( 'add_to_cart_redirect', 'WC_Bookings_Cart::add_to_cart_redirect' );
		}

	}

	/**
	 * OPC Single-product bookings add-to-cart template
	 *
	 * @param  int  $opc_post_id
	 * @return void
	 */
	public static function opc_single_add_to_cart_booking( $opc_post_id ) {

		global $product;

		ob_start();

		// Prepare form
		$booking_form = new WC_Booking_Form( $product );

		// Get template
		woocommerce_get_template( 'single-product/add-to-cart/booking.php', array( 'booking_form' => $booking_form ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );

		echo str_replace( array( '<form class="cart" method="post" enctype=\'multipart/form-data\'', '</form>' ), array( '<div class="cart" ', '</div>' ), ob_get_clean() );
	}


}
new WCOPC_Compat_Bookings();