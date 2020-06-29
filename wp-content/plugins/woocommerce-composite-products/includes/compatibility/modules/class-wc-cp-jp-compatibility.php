<?php
/**
 * WC_CP_JP_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.13.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jetpack compatibility.
 *
 * @version  4.0.0
 */
class WC_CP_JP_Compatibility {

	public static function init() {
		// Lazy Images module compatibility.
		add_filter( 'jetpack_lazy_images_skip_image_with_attributes', array( __CLASS__, 'skip_lazy_load' ), 1000 );
	}

	/**
	 * Disable Jetpack's Lazy Load module when populating component options image data.
	 *
	 * @param  boolean  $skip
	 * @return boolean
	 */
	public static function skip_lazy_load( $skip ) {

		if ( ( doing_action( 'woocommerce_composite_add_to_cart' ) && ( did_action( 'woocommerce_composite_component_selections_single' ) || did_action( 'woocommerce_composite_component_selections_progressive' ) || did_action( 'woocommerce_composite_component_selections_paged' ) ) ) || doing_action( 'wc_ajax_woocommerce_show_component_options' ) ) {
			$skip = true;
		}

		return $skip;
	}
}

WC_CP_JP_Compatibility::init();
