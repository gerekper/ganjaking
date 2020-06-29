<?php
/**
 * WC_CP_TAWS_Variation_Swatches_Compatibility
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeAlien Variation Swatches for WooCommerce
 *
 * @version  4.0.0
 */
class WC_CP_TAWS_Variation_Swatches_Compatibility {

	public static function init() {

		// Support for ThemeAlien Variation Swatches for WooCommerce.
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'tawc_variation_swatches_form_support' ), 5 );
	}

	/**
	 * Add footer script to support ThemeAlien's Variation Swatches.
	 *
	 * @return void
	 */
	public static function tawc_variation_swatches_form_support() {

		if ( wp_script_is( 'wc-add-to-cart-composite' ) ) {

			$js = "

				jQuery( document.body ).on( 'wc-composite-initializing', function( event, composite ) {

					if ( typeof( jQuery.fn.tawcvs_variation_swatches_form ) === 'function' ) {

						composite.actions.add_action( 'component_scripts_initialized', function( step ) {
							if ( 'variable' === step.get_selected_product_type() ) {
								step.\$component_summary_content.tawcvs_variation_swatches_form();
							}
						}, 10, this );
					}
				} );

			";

			echo "\n<script type=\"text/javascript\">\njQuery(function($) { $js });\n</script>\n";
		}
	}
}

WC_CP_TAWS_Variation_Swatches_Compatibility::init();
