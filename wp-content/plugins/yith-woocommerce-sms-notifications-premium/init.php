<?php
/**
 * Plugin Name: YITH WooCommerce SMS Notifications Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-sms-notifications/
 * Description: <code><strong>YITH WooCommerce SMS Notifications</strong></code> allows you to be always updated about the orders submitted in your store even if you're not in front of your computer. The plugin sends SMS texts to your and/or your customer's phone number automatically every time there's a new order or change in the order status. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-woocommerce-sms-notifications
 * Version: 1.4.7
 * Author URI: https://yithemes.com/
 * WC requires at least: 4.0.0
 * WC tested up to: 4.3.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function ywsn_install_woocommerce_premium_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce SMS Notifications is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-sms-notifications' ); ?></p>
	</div>
	<?php
}

if ( ! defined( 'YWSN_VERSION' ) ) {
	define( 'YWSN_VERSION', '1.4.7' );
}

if ( ! defined( 'YWSN_INIT' ) ) {
	define( 'YWSN_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWSN_SLUG' ) ) {
	define( 'YWSN_SLUG', 'yith-woocommerce-sms-notifications' );
}

if ( ! defined( 'YWSN_SECRET_KEY' ) ) {
	define( 'YWSN_SECRET_KEY', 'UyoJ3TbaYVjTCN1VGRiC' );
}

if ( ! defined( 'YWSN_PREMIUM' ) ) {
	define( 'YWSN_PREMIUM', '1' );
}

if ( ! defined( 'YWSN_FILE' ) ) {
	define( 'YWSN_FILE', __FILE__ );
}

if ( ! defined( 'YWSN_DIR' ) ) {
	define( 'YWSN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWSN_URL' ) ) {
	define( 'YWSN_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWSN_ASSETS_URL' ) ) {
	define( 'YWSN_ASSETS_URL', YWSN_URL . 'assets' );
}

if ( ! defined( 'YWSN_TEMPLATE_PATH' ) ) {
	define( 'YWSN_TEMPLATE_PATH', YWSN_DIR . 'templates' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWSN_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWSN_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YWSN_DIR );

function ywsn_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-woocommerce-sms-notifications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* === Global YITH WooCommerce SMS Notifications  === */
	YITH_WSN();

}

add_action( 'ywsn_init', 'ywsn_init' );

function ywsn_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ywsn_install_woocommerce_premium_admin_notice' );
	} else {
		do_action( 'ywsn_init' );
	}

}

add_action( 'plugins_loaded', 'ywsn_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'YITH_WSN' ) ) {

	/**
	 * Unique access to instance of YITH_WC_SMS_Notifications
	 *
	 * @return  YITH_WC_SMS_Notifications
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function YITH_WSN() { //phpcs:ignore

		// Load required classes and functions
		require_once( YWSN_DIR . 'class-yith-wc-sms-notifications.php' );

		return YITH_WC_SMS_Notifications::get_instance();

	}
}
