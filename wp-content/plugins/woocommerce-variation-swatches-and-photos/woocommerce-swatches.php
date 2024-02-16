<?php

/*
 * Plugin Name: WooCommerce Variation Swatches and Photos
 * Plugin URI: https://woocommerce.com/products/variation-swatches-and-photos/
 * Description: WooCommerce Swatches and Photos allows you to configure colors and photos for shoppers on your site to use when picking variations.
 * Version: 3.1.9
 * Author: Element Stark
 * Author URI: https://elementstark.com/solutions/woocommerce-variation-swatches-and-photos/
 * Requires at least: 3.5
 * Tested up to: 6.4
 * Domain Path: /i18n/languages/
 * Copyright: © 2009-2024 Element Stark LLC.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * WC requires at least: 7.0
 * WC tested up to: 8.5
 * Woo: 18697:37bea8d549df279c8278878d081b062f
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '37bea8d549df279c8278878d081b062f', '18697' );


if ( is_woocommerce_active() ) {

	require 'classes/class-wc-swatches-compatibility.php';

	add_action( 'init', 'wc_swatches_and_photos_load_textdomain', 0 );

	function wc_swatches_and_photos_load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_swatches_and_photos' );
		load_textdomain( 'wc_swatches_and_photos', WP_LANG_DIR . '/woocommerce/wc_swatches_and_photos-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_swatches_and_photos', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	// Declare support for features
	add_action( 'before_woocommerce_init', function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );

	add_action( 'plugins_loaded', 'wc_swatches_on_plugin_loaded' );

	function wc_swatches_on_plugin_loaded() {
		require 'woocommerce-swatches-main.php';
		$GLOBALS['woocommerce_swatches'] = new WC_SwatchesPlugin();
	}
}
