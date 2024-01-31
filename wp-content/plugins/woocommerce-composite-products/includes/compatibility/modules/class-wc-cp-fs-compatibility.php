<?php
/**
 * WC_CP_FS_Compatibility class
 *
 * @package  Woo Composite Products
 * @since    7.0.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flatsome integration.
 *
 * @version  8.10.5
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

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wc-composite-single-css' );
		wp_enqueue_script( 'wc-add-to-cart-composite' );

		wp_register_script( 'wc-composite-flatsome-quickview', WC_CP()->plugin_url() . '/assets/js/frontend/integrations/composite-flatsome-quickview' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'underscore', 'backbone', 'wp-util', 'wc-add-to-cart-variation' ), WC_CP()->version, true );
		wp_script_add_data( 'wc-composite-flatsome-quickview', 'strategy', 'defer' );
		wp_enqueue_script( 'wc-composite-flatsome-quickview' );
	}
}

WC_CP_FS_Compatibility::init();
