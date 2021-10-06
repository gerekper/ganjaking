<?php
/**
 * WC_CP_FS_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    7.0.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flatsome integration.
 *
 * @version  7.0.7
 */
class WC_CP_FS_Compatibility {

	public static function init() {
		// Add hooks if the active parent theme is Flatsome.
		add_action( 'after_setup_theme', array( __CLASS__, 'maybe_add_hooks' ) );
	}

	/**
	 * Add hooks if the active parent theme is Flatsome.
	 */
	public static function maybe_add_hooks() {

		if ( function_exists( 'flatsome_quickview' ) ) {
			// Initialize bundles in quick view modals.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_quickview_integration' ), 999 );
		}
	}

	/**
	 * Initializes bundles in quick view modals.
	 *
	 * @return array
	 */
	public static function add_quickview_integration() {

		wp_enqueue_style( 'wc-composite-single-css' );
		wp_enqueue_script( 'wc-add-to-cart-composite' );
		wp_add_inline_script( 'wc-add-to-cart-composite',
		'
			jQuery( document ).on( "mfpOpen", function( e ) {

				jQuery( ".composite_form .composite_data" ).each( function() {
						jQuery( this ).wc_composite_form();
				} );

			} );
		' );
	}
}

WC_CP_FS_Compatibility::init();
