<?php
/**
 * Plugin Name: YITH Frontend Manager for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-frontend-manager/
 * Description: <code><strong>YITH Frontend Manager for WooCommerce</strong></code> allows you to manage a WooCommerce based shop without accessing the admin area. Thanks to a handy frontend interface, your partners and you will be able to edit and manage products, orders, stats and coupons in a highly professional way. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-frontend-manager-for-woocommerce
 * Version: 1.32.0
 * Author URI: https://yithemes.com/
 *
 * Requires at least: 6.1
 * Tested up to: 6.3
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @package YITH\FrontendManager
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Stop activation if the premium version of the same plugin is still active.
if ( defined( 'YITH_WCFM_VERSION' ) ) {
	return;
}

! defined( 'YITH_WCFM_INIT' ) && define( 'YITH_WCFM_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCFM_VERSION' ) && define( 'YITH_WCFM_VERSION', '1.32.0' );
! defined( 'YITH_WCFM_DB_VERSION' ) && define( 'YITH_WCFM_DB_VERSION', '1.0.1' );
! defined( 'YITH_WCFM_SLUG' ) && define( 'YITH_WCFM_SLUG', 'yith-frontend-manager-for-woocommerce' );
! defined( 'YITH_WCFM_FILE' ) && define( 'YITH_WCFM_FILE', __FILE__ );
! defined( 'YITH_WCFM_PATH' ) && define( 'YITH_WCFM_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCFM_CLASS_PATH' ) && define( 'YITH_WCFM_CLASS_PATH', YITH_WCFM_PATH . 'includes/' );
! defined( 'YITH_WCFM_LIB_PATH' ) && define( 'YITH_WCFM_LIB_PATH', YITH_WCFM_CLASS_PATH . 'lib/' );
! defined( 'YITH_WCFM_SECTIONS_CLASS_PATH' ) && define( 'YITH_WCFM_SECTIONS_CLASS_PATH', YITH_WCFM_CLASS_PATH . 'sections/' );
! defined( 'YITH_WCFM_URL' ) && define( 'YITH_WCFM_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCFM_ASSETS_URL' ) && define( 'YITH_WCFM_ASSETS_URL', YITH_WCFM_URL . 'assets/' );
! defined( 'YITH_WCFM_SCRIPT_URL' ) && define( 'YITH_WCFM_SCRIPT_URL', YITH_WCFM_ASSETS_URL . 'js/' );
! defined( 'YITH_WCFM_STYLE_URL' ) && define( 'YITH_WCFM_STYLE_URL', YITH_WCFM_ASSETS_URL . 'css/' );
! defined( 'YITH_WCFM_TEMPLATE_PATH' ) && define( 'YITH_WCFM_TEMPLATE_PATH', YITH_WCFM_PATH . 'templates/' );
! defined( 'YITH_WCFM_TEMPLATE_URL' ) && define( 'YITH_WCFM_TEMPLATE_URL', YITH_WCFM_URL . 'templates/' );
! defined( 'YITH_WCFM_SECTIONS_PATH' ) && define( 'YITH_WCFM_SECTIONS_PATH', YITH_WCFM_TEMPLATE_PATH . 'sections/' );
! defined( 'YITH_WCFM_PREMIUM' ) && define( 'YITH_WCFM_PREMIUM', true );
! defined( 'YITH_WCFM_PREMIUM_CLASS_PATH' ) && define( 'YITH_WCFM_PREMIUM_CLASS_PATH', YITH_WCFM_PATH . 'includes/extends/' );
! defined( 'YITH_WCFM_SECRET_KEY' ) && define( 'YITH_WCFM_SECRET_KEY', 'pJvOjwvWD0OarQRiIpx7' );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once YITH_WCFM_PATH . 'plugin-fw/yit-plugin-registration-hook.php';
}

require_once YITH_WCFM_CLASS_PATH . 'functions.yith-frontend-manager.php';

if ( ! function_exists( 'yith_wcfm_load_textdomain' ) ) {
	/**
	 * Load plugin text domain
	 */
	function yith_wcfm_load_textdomain() {
		load_plugin_textdomain( 'yith-frontend-manager-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'YITH_Frontend_Manager' ) ) {
	/**
	 * Unique access to instance of YITH_Frontend_Manager class
	 *
	 * @return YITH_Frontend_Manager|YITH_Frontend_Manager_Premium
	 * @since 1.0.0
	 */
	function YITH_Frontend_Manager() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcfm_install_woocommerce_admin_notice' );
		} else {
			// Load required classes and functions.
			require_once YITH_WCFM_CLASS_PATH . 'class.yith-frontend-manager.php';

			if ( defined( 'YITH_WCFM_PREMIUM' ) && defined( 'YITH_WCFM_PREMIUM_CLASS_PATH' ) && file_exists( YITH_WCFM_PREMIUM_CLASS_PATH . 'class.yith-frontend-manager-premium.php' ) ) {
				require_once YITH_WCFM_PREMIUM_CLASS_PATH . 'class.yith-frontend-manager-premium.php';
				return YITH_Frontend_Manager_Premium::instance();
			}

			return YITH_Frontend_Manager::instance();
		}
	}
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCFM_PATH . 'plugin-fw/init.php' ) ) {
	require_once YITH_WCFM_PATH . 'plugin-fw/init.php';
}

yit_maybe_plugin_fw_loader( YITH_WCFM_PATH );

/* Plugin Action */
add_action( 'init', 'yith_wcfm_load_textdomain' );
add_action( 'plugins_loaded', 'YITH_Frontend_Manager', 11 );

/* Register Activation Hook */
register_activation_hook( YITH_WCFM_FILE, 'yith_wcfm_setup' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );
