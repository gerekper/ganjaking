<?php
/**
 * Plugin Name: YITH FAQ for WordPress & WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-faq-plugin-for-wordpress/
 * Description: <code><strong>YITH FAQ for WordPress & WooCommerce</strong></code> allows you to introduce an efficient FAQ system on any page of your WordPress or WooCommerce-based website. You can also add custom FAQs for any page. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-faq-plugin-for-wordpress
 * Version: 2.15.0
 * Author URI: https://yithemes.com/
 * WC requires at least: 7.9.0
 * WC tested up to: 8.1.x
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

! defined( 'YITH_FWP_VERSION' ) && define( 'YITH_FWP_VERSION', '2.15.0' );
! defined( 'YITH_FWP_INIT' ) && define( 'YITH_FWP_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_FWP_SLUG' ) && define( 'YITH_FWP_SLUG', 'yith-faq-plugin-for-wordpress' );
! defined( 'YITH_FWP_SECRET_KEY' ) && define( 'YITH_FWP_SECRET_KEY', 'It5GLd4OgQYdE8MntVGj' );
! defined( 'YITH_FWP_PREMIUM' ) && define( 'YITH_FWP_PREMIUM', '1' );
! defined( 'YITH_FWP_FILE' ) && define( 'YITH_FWP_FILE', __FILE__ );
! defined( 'YITH_FWP_DIR' ) && define( 'YITH_FWP_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_FWP_URL' ) && define( 'YITH_FWP_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_FWP_ASSETS_URL' ) && define( 'YITH_FWP_ASSETS_URL', YITH_FWP_URL . 'assets' );
! defined( 'YITH_FWP_SHORTCODE_POST_TYPE' ) && define( 'YITH_FWP_SHORTCODE_POST_TYPE', 'yith_faq_shortcode' );
! defined( 'YITH_FWP_FAQ_POST_TYPE' ) && define( 'YITH_FWP_FAQ_POST_TYPE', 'yith_faq' );
! defined( 'YITH_FWP_FAQ_TAXONOMY' ) && define( 'YITH_FWP_FAQ_TAXONOMY', 'yith_faq_cat' );

/* Plugin Framework Version Check 
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_FWP_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_FWP_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_FWP_DIR );*/

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

/**
 * Run plugin
 *
 * @return  void
 * @since   1.0.0
 */
function yith_fwp_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-faq-plugin-for-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	YITH_FWP();

}

add_action( 'yith_fwp_init', 'yith_fwp_init' );

/**
 * Initialize plugin
 *
 * @return void
 * @since   1.0.0
 */
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
	 */
	function YITH_FWP() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

		// Load required classes and functions.
		require_once YITH_FWP_DIR . 'class-yith-faq-plugin-for-wordpress.php';

		return YITH_FAQ_Plugin_For_WordPress::get_instance();

	}
}

add_action( 'before_woocommerce_init', 'yith_fwp_declare_hpos_compatibility' );

/**
 * Declare HPOS compatibility
 *
 * @return void
 * @since  2.6.0
 */
function yith_fwp_declare_hpos_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
