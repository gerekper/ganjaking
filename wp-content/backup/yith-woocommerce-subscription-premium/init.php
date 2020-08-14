<?php
/*
Plugin Name: YITH WooCommerce Subscription Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-subscription/
Description: <code><strong>YITH WooCommerce Subscription</strong></code> allows enabling automatic recurring payments on your products. Once you buy a subscription-based product, the plugin will renew the payment automatically based on your own settings. Perfect for any kind of subscriptions, like magazines, software and so on. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
Version: 1.7.8
Author: YITH
Author URI: https://yithemes.com/
Text Domain: yith-woocommerce-subscription
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 4.2.0
*/

/*
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Free version deactivation if installed __________________
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWSBS_FREE_INIT', plugin_basename( __FILE__ ) );


! defined( 'YITH_YWSBS_DIR' ) && define( 'YITH_YWSBS_DIR', plugin_dir_path( __FILE__ ) );


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWSBS_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWSBS_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWSBS_DIR );

// Define constants ________________________________________
 ! defined( 'YITH_YWSBS_VERSION' ) && define( 'YITH_YWSBS_VERSION', '1.7.8' );
! defined( 'YITH_YWSBS_PREMIUM' ) && define( 'YITH_YWSBS_PREMIUM', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWSBS_INIT' ) && define( 'YITH_YWSBS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWSBS_FILE' ) && define( 'YITH_YWSBS_FILE', __FILE__ );
! defined( 'YITH_YWSBS_URL' ) && define( 'YITH_YWSBS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_YWSBS_ASSETS_URL' ) && define( 'YITH_YWSBS_ASSETS_URL', YITH_YWSBS_URL . 'assets' );
! defined( 'YITH_YWSBS_TEMPLATE_PATH' ) && define( 'YITH_YWSBS_TEMPLATE_PATH', YITH_YWSBS_DIR . 'templates' );
! defined( 'YITH_YWSBS_INC' ) && define( 'YITH_YWSBS_INC', YITH_YWSBS_DIR . '/includes/' );
! defined( 'YITH_YWSBS_SLUG' ) && define( 'YITH_YWSBS_SLUG', 'yith-woocommerce-subscription' );
! defined( 'YITH_YWSBS_SECRET_KEY' ) && define( 'YITH_YWSBS_SECRET_KEY', 'TfE4SfRN6mtm8qpsumyL' );
! defined( 'YITH_YWSBS_TEST_ON' ) && define( 'YITH_YWSBS_TEST_ON', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) );
if ( ! defined( 'YITH_YWSBS_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWSBS_SUFFIX', $suffix );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWSBS_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWSBS_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWSBS_DIR );


/**
 * Print a notice if WooCommerce is not installed.
 *
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function yith_ywsbs_install_woocommerce_admin_notice_premium() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Subscription is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-subscription' ); ?></p>
	</div>
	<?php
}

/**
 * Check WC installation and update the database if necessary.
 *
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function yith_ywsbs_premium_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywsbs_install_woocommerce_admin_notice_premium' );
	} else {
		do_action( 'yith_ywsbs_init' );

		// check for update table
		if ( function_exists( 'yith_ywsbs_update_db_check' ) ) {
			yith_ywsbs_update_db_check();
		}
	}
}
add_action( 'plugins_loaded', 'yith_ywsbs_premium_install', 11 );


/**
 * Start the game.
 *
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function yith_ywsbs_premium_constructor() {
	load_plugin_textdomain( 'yith-woocommerce-subscription', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	require_once YITH_YWSBS_INC . 'functions.yith-wc-subscription.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-activity.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-order.php';
	require_once YITH_YWSBS_INC . 'class.ywsbs-susbscription-helper.php';
	require_once YITH_YWSBS_INC . 'class.ywsbs-susbscription.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-coupons.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-cart.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-admin.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-cron.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-my-account.php';
	require_once YITH_YWSBS_INC . 'gateways/paypal/class.yith-wc-subscription-paypal.php';
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-privacy.php';

	if ( is_admin() ) {
		YITH_WC_Subscription_Admin();
	}

	YITH_WC_Subscription();
}

add_action( 'yith_ywsbs_init', 'yith_ywsbs_premium_constructor' );
register_deactivation_hook( __FILE__, 'yith_ywsbs_remove_flush_rewrite_rule_option' );
/**
 * Remove flush rewrite rule option.
 */
function yith_ywsbs_remove_flush_rewrite_rule_option(){
	delete_option('ywsbs_queue_flush_rewrite_rules');
}