<?php
/**
 * WC_PB_PPEC_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.6.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PayPal Express Checkout Compatibility.
 *
 * @version  6.6.1
 */
class WC_PB_PPEC_Compatibility {

	public static function init() {
		add_action( 'woocommerce_bundle_add_to_cart', array( __CLASS__, 'handle_ppec_quickpay_buttons_visibility' ) );
	}

	/**
	 * Enable/Disable PayPal Express Checkout Quick-pay buttons based on the validity of the Bundle's configuration.
	 *
	 * @since 6.6.1
	 *
	 */
	public static function handle_ppec_quickpay_buttons_visibility() {

		?><script type="text/javascript">
			jQuery( function( $ ) {

				$( '.bundle_form .bundle_data' ).on( 'woocommerce-product-bundle-validation-status-changed', function( e, bundle ) {

					// Set time delay to make sure our code runs after the ppec 'validate_form' function.
					setTimeout( function() {

						if ( 'fail' === bundle.api.get_bundle_validation_status() ) {
							$( '#woo_pp_ec_button_product' ).trigger( 'disable' );
						} else {
							$( '#woo_pp_ec_button_product' ).trigger( 'enable' );
						}

					}, 1 );

				} );
			} );
		</script><?php
	}
}

WC_PB_PPEC_Compatibility::init();
