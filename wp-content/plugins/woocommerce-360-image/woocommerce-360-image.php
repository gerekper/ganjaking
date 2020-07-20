<?php
/**
 * Plugin Name: WooCommerce 360° Image
 * Plugin URI: https://woocommerce.com/products/woocommerce-360-image/
 * Description: Add a 360° image rotation display your product listings in WooCommerce.
 * Version: 1.1.16
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL-2.0+
 * Domain: woocommerce-360-image
 * Woo: 512186:24eb2cfa3738a66bf3b2587876668cd2
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Copyright (c) 2020 WooCommerce
 *
 * @package woocommerce-360-image
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'WC_360_IMAGE_VERSION' ) ) {
	define( 'WC_360_IMAGE_VERSION', '1.1.16' ); // WRCS: DEFINED_VERSION.
}

// Load main class and register activation function.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360.php';
register_activation_hook( __FILE__, array( 'WC_360_Image', 'activate' ) );

// Plugin init hook.
add_action( 'plugins_loaded', 'woocommerce_360_image_init' );

/**
 * Initialize plugin.
 */
function woocommerce_360_image_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_360_image_woocommerce_deactivated' );
		return;
	}

	// Include plugin classes.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360-display.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360-settings.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360-meta.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360-shortcode.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc360-utils.php';

	// Initialize classes.
	WC_360_Image::get_instance();
	WC_360_Image_Settings::get_instance();
	WC_360_Image_Meta::get_instance();
	add_action( 'wp', array( 'WC_360_Image_Display', 'get_instance' ) );
	add_action( 'wp', array( 'WC_360_Image_Shortcode', 'get_instance' ) );
}

/**
 * WooCommerce Deactivated Notice.
 */
function woocommerce_360_image_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce 360 Image requires %s to be installed and active.', 'woocommerce-360-image' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
