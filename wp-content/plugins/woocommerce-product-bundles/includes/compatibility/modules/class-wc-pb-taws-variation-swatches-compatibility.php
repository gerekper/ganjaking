<?php
/**
 * WC_PB_TAWS_Variation_Swatches_Compatibility
 *
 * @package  WooCommerce Product Bundles
 * @since    5.9.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeAlien Variation Swatches for WooCommerce
 *
 * @version  5.9.2
 */
class WC_PB_TAWS_Variation_Swatches_Compatibility {

	public static function init() {

		// Support for ThemeAlien Variation Swatches for WooCommerce.
		add_action( 'woocommerce_bundle_add_to_cart', array( __CLASS__, 'tawc_variation_swatches_form_support' ) );
	}

	/**
	 * Add footer script to support ThemeAlien's Variation Swatches.
	 *
	 * @return void
	 */
	public static function tawc_variation_swatches_form_support() {

		wc_enqueue_js( "

			var init_tawcvs_variation_swatches_form = function() {

				if ( typeof jQuery.fn.tawcvs_variation_swatches_form === 'function' ) {
					$( '.variations_form' ).tawcvs_variation_swatches_form();
					$( document.body ).trigger( 'tawcvs_initialized' );
				}
			};

			if ( jQuery( '.bundle_form .bundle_data' ).length > 0 ) {
				init_tawcvs_variation_swatches_form();
			}

		" );
	}
}

WC_PB_TAWS_Variation_Swatches_Compatibility::init();
