<?php
/**
 * Extension Compatibilty
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    2.1.0
 * @version  2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Free_Gift_Coupons_Compatibility Class.
 *
 * Load classes for making Free Gift Coupons compatible with other plugins.
 */
class WC_Free_Gift_Coupons_Compatibility { 

	public function __construct() {

		// Initialize.
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}

	/**
	 * Init compatibility classes.
	 */
	public static function init() {

		// Smart Coupons support.
		if ( class_exists( 'WC_Smart_Coupons' ) ) {
			include_once  'modules/class-wc-fgc-smart-coupons-compatibility.php' ;
		}

		// Subscriptions support.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			include_once  'modules/class-wc-fgc-subscriptions-compatibility.php' ;
		}

		// Deactivate functionality from mini-extensions.
		self::unload();


	}

	/**
	 * Unload mini-extensions.
	 * 
	 * @since 3.0.0
	 */
	public static function unload() {
		// Deactivate functionality added by WC Free Gift Coupons - Choose Variation mini-extension.
		if ( class_exists( 'WC_FGC_Choose_Variation' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_FGC_Choose_Variation', 'init' ) );
		}
	}

}

WC_Free_Gift_Coupons_Compatibility::init();
