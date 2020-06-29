<?php
/*
*Plugin Name: YITH WooCommerce Multi Vendor Premium
*Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-multi-vendor/
*Description: <code><strong>YITH WooCommerce Multi Vendor</strong></code> turns your website into a real marketplace, where it's your partners who will add new products independently and you earn a percentage commission on every sale. Take advantage of this great opportunity to increase your earnings in a simple way and for good. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
*Author: YITH
*Text Domain: yith-woocommerce-product-vendors
*Version: 3.6.2
*Author URI: https://yithemes.com/
*
* WC requires at least: 3.8
* WC tested up to: 4.2
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! empty( $_GET['yith_disable_wcmv'] ) && 'yes' == $_GET['yith_disable_wcmv'] ){
	return false;
}

/**
 * Load actiovation utility functions
 */
require_once 'includes/functions.yith-plugin-activation.php';

if ( ! function_exists( 'WC' ) ) {
    add_action( 'admin_notices', 'install_premium_woocommerce_admin_notice' );
    return;
}

! defined( 'YITH_WPV_PREMIUM' ) && define( 'YITH_WPV_PREMIUM', '1' );
! defined( 'YITH_WPV_INIT' )    && define( 'YITH_WPV_INIT', plugin_basename( __FILE__ ) );

/**
 * Check if a free version currently active and try disabling before activating this one
 */
if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WPV_FREE_INIT', YITH_WPV_INIT );

/**
 * Check if a jetpack module is currently active and try disabling before activating this one
 */
global $yith_jetpack_1;
yith_deactive_jetpack_module( $yith_jetpack_1, 'YITH_WPV_PREMIUM', YITH_WPV_INIT );

//  Stop activation if the premium version of the same plugin is still active
if ( defined( 'YITH_WPV_VERSION' ) ) {
    return;
}

/* Load YWCM text domain */
if( ! function_exists( 'yith_wcmv_load_textdomain' ) ){
	/* Load Plugin Textdomanin */
	function yith_wcmv_load_textdomain(){
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// @todo Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'yith-woocommerce-product-vendors' );

		unload_textdomain( 'yith-woocommerce-product-vendors' );
		load_textdomain( 'yith-woocommerce-product-vendors', WP_LANG_DIR . '/plugins/yith-woocommerce-product-vendors-' . $locale . '.mo' );
		load_plugin_textdomain( 'yith-woocommerce-product-vendors', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

add_action( 'init', 'yith_wcmv_load_textdomain' );

! defined( 'YITH_WPV_VERSION' )         && define( 'YITH_WPV_VERSION', '3.6.2' );
! defined( 'YITH_WPV_DB_VERSION' )      && define( 'YITH_WPV_DB_VERSION', '1.1.13' );
! defined( 'YITH_WPV_SLUG' )            && define( 'YITH_WPV_SLUG', 'yith-woocommerce-product-vendors' );
! defined( 'YITH_WPV_SECRET_KEY' )      && define( 'YITH_WPV_SECRET_KEY', '6NBH2Snt7DFU4J02vtgl' );
! defined( 'YITH_WPV_FILE' )            && define( 'YITH_WPV_FILE', __FILE__ );
! defined( 'YITH_WPV_PATH' )            && define( 'YITH_WPV_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WPV_URL' )             && define( 'YITH_WPV_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WPV_ASSETS_URL' )      && define( 'YITH_WPV_ASSETS_URL', YITH_WPV_URL . 'assets/' );
! defined( 'YITH_WPV_TEMPLATE_PATH' )   && define( 'YITH_WPV_TEMPLATE_PATH', YITH_WPV_PATH . 'templates/' );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

if ( ! function_exists( 'YITH_Vendors' ) ) {
	/**
	 * Unique access to instance of YITH_Vendors class
	 *
	 * @return YITH_Vendors|YITH_Vendors_Premium
	 * @since 1.0.0
	 */
	function YITH_Vendors() {
		// Load required classes and functions
		require_once( YITH_WPV_PATH . 'includes/class.yith-vendors.php' );

		if ( defined( 'YITH_WPV_PREMIUM' ) && file_exists( YITH_WPV_PATH . 'includes/class.yith-vendors-premium.php' ) ) {
			/* User Switching Support */
			add_filter( 'login_redirect', 'yith_wcmv_switch_back_redirection_url', 10, 3 );

			require_once( YITH_WPV_PATH . 'includes/class.yith-vendors-premium.php' );
			return YITH_Vendors_Premium::instance();
		}

		return YITH_Vendors::instance();
	}
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WPV_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WPV_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WPV_PATH  );

/**
 * Instance main plugin class
 */
YITH_Vendors();

register_activation_hook( YITH_WPV_FILE, 'YITH_Commissions::create_commissions_table' );
register_activation_hook( YITH_WPV_FILE, 'YITH_Vendors_Payments::create_transaction_table' );
register_activation_hook( YITH_WPV_FILE, 'YITH_Vendors_Admin_Premium::create_plugins_page' );
register_activation_hook( YITH_WPV_FILE, 'YITH_Vendors::add_vendor_role' );
register_deactivation_hook( YITH_WPV_FILE, 'YITH_Vendors::setup' );
register_deactivation_hook( YITH_WPV_FILE, 'YITH_Vendors::remove_vendor_role' );
