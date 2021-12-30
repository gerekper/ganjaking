<?php
/**
 * WC_PB_FS_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.3.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flatsome integration.
 *
 * @version  6.3.6
 */
class WC_PB_FS_Compatibility {

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
			// Resolves image update mixups in quickview modals.
			add_filter( 'woocommerce_bundled_product_gallery_classes', array( __CLASS__, 'bundled_product_gallery_classes' ) );
			// Lowers the responsive styling breakpoint to prevent issues in quickview modals.
			add_filter( 'woocommerce_bundle_front_end_params', array( __CLASS__, 'adjust_responsive_breakpoint' ), 10 );
		}
	}

	/**
	 * Initializes bundles in quick view modals.
	 *
	 * @return array
	 */
	public static function add_quickview_integration() {

		wp_enqueue_style( 'wc-bundle-css' );
		wp_enqueue_script( 'wc-add-to-cart-bundle' );
		wp_add_inline_script( 'wc-add-to-cart-bundle',
		'
			jQuery( document ).on( "mfpOpen", function( e ) {

				jQuery( ".bundle_form .bundle_data" ).each( function() {

					var $bundle_data    = jQuery( this ),
						$composite_form = $bundle_data.closest( ".composite_form" );

					if ( $composite_form.length === 0 ) {
						$bundle_data.wc_pb_bundle_form();
					}

				} );

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

WC_PB_FS_Compatibility::init();
