<?php
/*
Plugin Name: YITH WooCommerce Order Tracking Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-order-tracking/
Description: With <code><strong>YITH WooCommerce Order Tracking Premium</strong></code> you can easy manage the order tracking information for WooCommerce orders. Select shipping company from a huge list of international carriers and let your customers track their orders with dynamically created track urls. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
Version: 1.6.4
Author: YITH
Author URI: http://yithemes.com/
Text Domain: yith-woocommerce-order-tracking
Domain Path: /languages/
WC requires at least: 3.2.0
WC tested up to: 4.2
*/

//region    ****    Check if prerequisites are satisfied before enabling and using current plugin

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_ywot_premium_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Order Tracking is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
	</div>
	<?php
}

/**
 * Check if a free version is currently active and try disabling before activating this one
 */
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWOT_FREE_INIT', plugin_basename( __FILE__ ) );


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


//endregion

//region    ****    Define constants  ****

defined( 'YITH_YWOT_INIT' ) || define( 'YITH_YWOT_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWOT_PREMIUM' ) || define( 'YITH_YWOT_PREMIUM', '1' );
defined( 'YITH_YWOT_SLUG' ) || define( 'YITH_YWOT_SLUG', 'yith-woocommerce-order-tracking' );
defined( 'YITH_YWOT_SECRET_KEY' ) || define( 'YITH_YWOT_SECRET_KEY', 'SD7S8bCFVCWOQjxutYoW' );
defined( 'YITH_YWOT_VERSION' ) || define( 'YITH_YWOT_VERSION', '1.6.4' );
defined( 'YITH_YWOT_FILE' ) || define( 'YITH_YWOT_FILE', __FILE__ );
defined( 'YITH_YWOT_DIR' ) || define( 'YITH_YWOT_DIR', plugin_dir_path( __FILE__ ) );
defined( 'YITH_YWOT_URL' ) || define( 'YITH_YWOT_URL', plugins_url( '/', __FILE__ ) );
defined( 'YITH_YWOT_ASSETS_URL' ) || define( 'YITH_YWOT_ASSETS_URL', YITH_YWOT_URL . 'assets' );
defined( 'YITH_YWOT_TEMPLATE_PATH' ) || define( 'YITH_YWOT_TEMPLATE_PATH', YITH_YWOT_DIR . 'templates' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWOT_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_YWOT_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_YWOT_DIR );

//endregion

function yith_ywot_premium_init() {

	/**
	 * Load text domain and start plugin
	 */
	load_plugin_textdomain( 'yith-woocommerce-order-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once( YITH_YWOT_DIR . 'class.yith-woocommerce-order-tracking.php' );
	require_once( YITH_YWOT_DIR . 'class.yith-woocommerce-order-tracking-premium.php' );
	require_once( YITH_YWOT_DIR . 'class.yith-tracking-data.php' );
	require_once( YITH_YWOT_DIR . 'class.carriers.php' );
	require_once( YITH_YWOT_DIR . 'functions.php' );

	global $YWOT_Instance;
	$YWOT_Instance = new Yith_WooCommerce_Order_Tracking_Premium();
}

add_action( 'yith_ywot_premium_init', 'yith_ywot_premium_init' );

if ( ! function_exists( 'YITH_YWOT' ) ) {
	function YITH_YWOT() {
		global $YWOT_Instance;

		return $YWOT_Instance;
	}
}

function yith_ywot_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywot_premium_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_ywot_premium_init' );
	}
}

add_action( 'plugins_loaded', 'yith_ywot_premium_install', 11 );