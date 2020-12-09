<?php
/**
 * WC_CP_PPEC_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    7.1.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PayPal Express Checkout Compatibility.
 *
 * @version  7.1.4
 */
class WC_CP_PPEC_Compatibility {

	public static function init() {
		add_action( 'woocommerce_composite_add_to_cart', array( __CLASS__, 'handle_ppec_quickpay_buttons_visibility' ) );
	}

	/**
	 * Enable/Disable PayPal Express Checkout Quick-pay buttons based on the validity of the Composite's configuration.
	 *
	 * @since 7.1.4
	 *
	 */
	public static function handle_ppec_quickpay_buttons_visibility() {

		?><script type="text/javascript">
			jQuery( function( $ ) {

				$( '.composite_data' ).on( 'wc-composite-initializing', function( event, composite ) {

					var update_ppec_buttons = function() {

						// Set time delay to make sure our code runs after the ppec 'validate_form' function.
						setTimeout( function() {

							if ( 'fail' === composite.api.get_composite_validation_status() ) {
								$( '#woo_pp_ec_button_product' ).trigger( 'disable' );
							} else {
								$( '#woo_pp_ec_button_product' ).trigger( 'enable' );
							}

						}, 1 );
					}

					composite.actions.add_action( 'composite_validation_status_changed', update_ppec_buttons, 100, this );
				} );

			} );
		</script><?php
	}
}

WC_CP_PPEC_Compatibility::init();
