<?php
/**
 * Plugin Name: YITH FAQ Plugin for WordPress Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-faq-plugin-for-wordpress/
 * Description: <code><strong>YITH FAQ Plugin for WordPress</strong></code> allows entering an efficient FAQ system on any page of your WordPress or WooCommerce based website. You can also add custom FAQs for any page. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-faq-plugin-for-wordpress
 * Version: 1.1.5
 * Author URI: https://yithemes.com/
 * WC tested up to: x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! defined( 'YITH_FWP_VERSION' ) ) {
	define( 'YITH_FWP_VERSION', '1.1.5' );
}

if ( ! defined( 'YITH_FWP_INIT' ) ) {
	define( 'YITH_FWP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_FWP_SLUG' ) ) {
	define( 'YITH_FWP_SLUG', 'yith-faq-plugin-for-wordpress' );
}

if ( ! defined( 'YITH_FWP_SECRET_KEY' ) ) {
	define( 'YITH_FWP_SECRET_KEY', 'It5GLd4OgQYdE8MntVGj' );
}

if ( ! defined( 'YITH_FWP_PREMIUM' ) ) {
	define( 'YITH_FWP_PREMIUM', '1' );
}

if ( ! defined( 'YITH_FWP_FILE' ) ) {
	define( 'YITH_FWP_FILE', __FILE__ );
}

if ( ! defined( 'YITH_FWP_DIR' ) ) {
	define( 'YITH_FWP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_FWP_URL' ) ) {
	define( 'YITH_FWP_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_FWP_ASSETS_URL' ) ) {
	define( 'YITH_FWP_ASSETS_URL', YITH_FWP_URL . 'assets' );
}

if ( ! defined( 'YITH_FWP_TEMPLATE_PATH' ) ) {
	define( 'YITH_FWP_TEMPLATE_PATH', YITH_FWP_DIR . 'templates' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_FWP_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_FWP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_FWP_DIR );

function yith_fwp_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-faq-plugin-for-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	YITH_FWP();

}

add_action( 'yith_fwp_init', 'yith_fwp_init' );

function yith_fwp_install() {

	do_action( 'yith_fwp_init' );

}

add_action( 'plugins_loaded', 'yith_fwp_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'YITH_FWP' ) ) {

	/**
	 * Unique access to instance of YITH_FAQ_Plugin_For_WordPress
	 *
	 * @return  YITH_FAQ_Plugin_For_WordPress
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function YITH_FWP() { //phpcs:ignore

		// Load required classes and functions
		require_once( YITH_FWP_DIR . 'class-yith-faq-plugin-for-wordpress.php' );

		return YITH_FAQ_Plugin_For_WordPress::get_instance();

	}
}
