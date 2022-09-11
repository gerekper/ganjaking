<?php
/**
 * Plugin Name: YITH WooCommerce Subscription Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-subscription/
 * Description: <code><strong>YITH WooCommerce Subscription</strong></code> allows enabling automatic recurring payments on your products. Once you buy a subscription-based product, the plugin will renew the payment automatically based on your own settings. Perfect for any kind of subscriptions, like magazines, software and so on. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 2.17.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-subscription
 * Domain Path: /languages/
 * WC requires at least: 6.5
 * WC tested up to: 6.7
 *
 * @package YITH WooCommerce Subscription
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Free version deactivation if installed.
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

// Define constants ________________________________________.

! defined( 'YITH_YWSBS_VERSION' ) && define( 'YITH_YWSBS_VERSION', '2.17.0' );
! defined( 'YITH_YWSBS_PREMIUM' ) && define( 'YITH_YWSBS_PREMIUM', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWSBS_INIT' ) && define( 'YITH_YWSBS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWSBS_FILE' ) && define( 'YITH_YWSBS_FILE', __FILE__ );
! defined( 'YITH_YWSBS_URL' ) && define( 'YITH_YWSBS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_YWSBS_ASSETS_URL' ) && define( 'YITH_YWSBS_ASSETS_URL', YITH_YWSBS_URL . 'assets' );
! defined( 'YITH_YWSBS_TEMPLATE_PATH' ) && define( 'YITH_YWSBS_TEMPLATE_PATH', YITH_YWSBS_DIR . 'templates' );
! defined( 'YITH_YWSBS_VIEWS_PATH' ) && define( 'YITH_YWSBS_VIEWS_PATH', YITH_YWSBS_DIR . 'views' );
! defined( 'YITH_YWSBS_INC' ) && define( 'YITH_YWSBS_INC', YITH_YWSBS_DIR . '/includes/' );
! defined( 'YITH_YWSBS_SLUG' ) && define( 'YITH_YWSBS_SLUG', 'yith-woocommerce-subscription' );
! defined( 'YITH_YWSBS_POST_TYPE' ) && define( 'YITH_YWSBS_POST_TYPE', 'ywsbs_subscription' );
! defined( 'YITH_YWSBS_SECRET_KEY' ) && define( 'YITH_YWSBS_SECRET_KEY', 'TfE4SfRN6mtm8qpsumyL' );
! defined( 'YITH_YWSBS_TEST_ON' ) && define( 'YITH_YWSBS_TEST_ON', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) );

$wp_upload_dir = wp_upload_dir();

if ( ! defined( 'YITH_YWSBS_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWSBS_SUFFIX', $suffix );
}

/**
 * Print a notice if WooCommerce is not installed.
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
 */
function yith_ywsbs_premium_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywsbs_install_woocommerce_admin_notice_premium' );
	} else {
		do_action( 'yith_ywsbs_init' );

		require_once 'includes/abstract.yith-wc-subscription-db.php';
		YITH_WC_Subscription_DB::install();

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
	require_once YITH_YWSBS_INC . 'class.yith-wc-subscription.php';

	YITH_WC_Subscription();
}

add_action( 'yith_ywsbs_init', 'yith_ywsbs_premium_constructor' );
register_activation_hook( __FILE__, 'yith_ywsbs_register_activation_hook' );
/**
 * Start recurring schedule for ac scheduling
 */
if ( ! function_exists( 'yith_ywsbs_register_activation_hook' ) ) {
	/**
	 * Register activation hook
	 */
	function yith_ywsbs_register_activation_hook() {
		$ve         = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
		$time_start = strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' );

		$has_hook_scheduled = as_next_scheduled_action( 'ywsbs_delivery_schedules_status_change' );

		! $has_hook_scheduled && as_schedule_recurring_action( $time_start, DAY_IN_SECONDS, 'ywsbs_delivery_schedules_status_change' );
	}
}

register_deactivation_hook( __FILE__, 'yith_ywsbs_remove_flush_rewrite_rule_option' );
/**
 * Remove flush rewrite rule option.
 */
if ( ! function_exists( 'yith_ywsbs_remove_flush_rewrite_rule_option' ) ) {
	/**
	 * Remove option ywsbs_queue_flush_rewrite_rules
	 */
	function yith_ywsbs_remove_flush_rewrite_rule_option() {
		delete_option( 'ywsbs_queue_flush_rewrite_rules' );
	}
}

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );