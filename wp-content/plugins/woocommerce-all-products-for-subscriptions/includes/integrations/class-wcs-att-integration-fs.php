<?php
/**
 * WCS_ATT_Integration_FS class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    3.1.18
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flatsome integration.
 *
 * @version  3.1.18
 */
class WCS_ATT_Integration_FS {

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

		wp_enqueue_script( 'wcsatt-single-product' );
		wp_add_inline_script( 'wcsatt-single-product',
		'
			jQuery( document ).on( "mfpOpen", function( e ) {

				jQuery( document.body ).trigger( "wcsatt-initialize" );

			} );
		' );
	}

	/**
	 * Lower the responsive styling breakpoint for Flatsome.
	 *
	 * @param  array  $params
	 * @return array
	 */
	public static function adjust_responsive_breakpoint( $params ) {
		$params[ 'responsive_breakpoint' ] = 320;
		return $params;
	}

	/**
	 * Resolve image update mixups in quickview modals.
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return array
	 */
	public static function bundled_product_gallery_classes( $bundled_item ) {
		return array( 'bundled_product_images' );
	}
}

WCS_ATT_Integration_FS::init();
