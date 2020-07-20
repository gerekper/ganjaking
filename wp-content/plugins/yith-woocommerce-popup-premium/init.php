<?php
/*
* Plugin Name: YITH WooCommerce Popup Premium
* Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-popup
* Description: <code><strong>YITH WooCommerce Popup</strong></code> allows creating and handling elegant popup windows in your online store. You have full control over the settings and the graphical layout and you'll be able to show them to your users based on specific actions they might carry out, like leaving the page (exit intent), loading the page or after following an external link and so on. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
* Version: 1.4.4
* Author: YITH
* Author URI: https://yithemes.com/
* Text Domain: yith-woocommerce-popup
* Domain Path: /languages/
* WC requires at least: 3.0.0
* WC tested up to: 4.3.0
**/


/*
 * @package YITH WooCommerce Popup Premium
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
// Define constants ________________________________________
if ( defined( 'YITH_YPOP_VERSION' ) ) {
	return;
} else {
	define( 'YITH_YPOP_VERSION', '1.4.4' );
}

if ( ! defined( 'YITH_YPOP_PREMIUM_INIT' ) ) {
	define( 'YITH_YPOP_PREMIUM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	define( 'YITH_YPOP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YPOP_FILE' ) ) {
	define( 'YITH_YPOP_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YPOP_DIR' ) ) {
	define( 'YITH_YPOP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YPOP_URL' ) ) {
	define( 'YITH_YPOP_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YPOP_ASSETS_URL' ) ) {
	define( 'YITH_YPOP_ASSETS_URL', YITH_YPOP_URL . 'assets' );
}

if ( ! defined( 'YITH_YPOP_ASSETS_PATH' ) ) {
	define( 'YITH_YPOP_ASSETS_PATH', YITH_YPOP_DIR . 'assets' );
}

if ( ! defined( 'YITH_YPOP_TEMPLATE_PATH' ) ) {
	define( 'YITH_YPOP_TEMPLATE_PATH', YITH_YPOP_DIR . 'templates' );
}

if ( ! defined( 'YITH_YPOP_TEMPLATE_URL' ) ) {
	define( 'YITH_YPOP_TEMPLATE_URL', YITH_YPOP_URL . 'templates' );
}

if ( ! defined( 'YITH_YPOP_INC' ) ) {
	define( 'YITH_YPOP_INC', YITH_YPOP_DIR . '/includes/' );
}


if ( ! defined( 'YITH_YPOP_SLUG' ) ) {
	define( 'YITH_YPOP_SLUG', 'yith-woocommerce-popup' );
}

if ( ! defined( 'YITH_YPOP_SECRET_KEY' ) ) {
	define( 'YITH_YPOP_SECRET_KEY', '2yKFxXRvRsVVJD9oCQg3' );
}

// Free version deactivation if installed __________________

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YPOP_FREE_INIT', plugin_basename( __FILE__ ) );


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YPOP_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YPOP_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YPOP_DIR );


// Registration hook  ________________________________________
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );



if ( ! function_exists( 'yith_ypop_install' ) ) {
	function yith_ypop_install() {
		do_action( 'yith_ypop_init' );
	}

	add_action( 'plugins_loaded', 'yith_ypop_install', 11 );
}



function yith_ypop_constructor() {

	// Load YWSL text domain ___________________________________
	load_plugin_textdomain( 'yith-woocommerce-popup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once YITH_YPOP_INC . 'functions.yith-popup.php';
	require_once YITH_YPOP_INC . 'class-yith-popup-icon.php';
	require_once YITH_YPOP_INC . 'class-yith-popup-newsletter.php';
	require_once YITH_YPOP_INC . 'newsletter-integration/MadMimi.php';
	require_once YITH_YPOP_INC . 'newsletter-integration/Mailchimp.php';
	// require_once( YITH_YPOP_INC . 'newsletter-integration/CampaignMonitor.php' );
	require_once YITH_YPOP_INC . 'newsletter-integration/Wysija.php';
	require_once YITH_YPOP_INC . 'class-yith-popup.php';
	if ( is_admin() ) {
		require_once YITH_YPOP_INC . 'class-yith-popup-admin.php';
		YITH_Popup_Admin();
	} else {
		require_once YITH_YPOP_INC . 'class-yith-popup-frontend.php';
		YITH_Popup_Frontend();
	}

	YITH_Popup();

}
add_action( 'yith_ypop_init', 'yith_ypop_constructor' );
