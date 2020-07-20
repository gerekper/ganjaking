<?php
/**
 * Plugin Name: YITH WooCommerce Points and Rewards Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-points-and-rewards/
 * Description: With <code><strong>YITH WooCommerce Points and Rewards</strong></code> you can start a rewarding program with points to gain your customers' loyalty. Your customers will be able to use their points to get discounts. It's a perfect marketing strategy for your store. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.8.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-points-and-rewards
 * Domain Path: /languages/
 * WC requires at least: 3.8.0
 * WC tested up to: 4.3.0
 **/

/*
 * @package YITH WooCommerce Points and Rewards Premium
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! defined( 'YITH_YWPAR_DIR' ) ) {
	define( 'YITH_YWPAR_DIR', plugin_dir_path( __FILE__ ) );
}


if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWPAR_FREE_INIT', plugin_basename( __FILE__ ) );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWPAR_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWPAR_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWPAR_DIR );

// Define constants ________________________________________.

! defined( 'YITH_YWPAR_VERSION' ) && define( 'YITH_YWPAR_VERSION', '1.8.0' );
! defined( 'YITH_YWPAR_PREMIUM' ) && define( 'YITH_YWPAR_PREMIUM', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWPAR_INIT' ) && define( 'YITH_YWPAR_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_YWPAR_FILE' ) && define( 'YITH_YWPAR_FILE', __FILE__ );
! defined( 'YITH_YWPAR_URL' ) && define( 'YITH_YWPAR_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_YWPAR_ASSETS_URL' ) && define( 'YITH_YWPAR_ASSETS_URL', YITH_YWPAR_URL . 'assets' );
! defined( 'YITH_YWPAR_TEMPLATE_PATH' ) && define( 'YITH_YWPAR_TEMPLATE_PATH', YITH_YWPAR_DIR . 'templates' );
! defined( 'YITH_YWPAR_INC' ) && define( 'YITH_YWPAR_INC', YITH_YWPAR_DIR . '/includes/' );
! defined( 'YITH_YWPAR_SLUG' ) && define( 'YITH_YWPAR_SLUG', 'yith-woocommerce-points-and-rewards' );
! defined( 'YITH_YWPAR_SECRET_KEY' ) && define( 'YITH_YWPAR_SECRET_KEY', 'BtvfSnvcDK1ZB1lgvJbY' );

if ( ! defined( 'YITH_YWPAR_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWPAR_SUFFIX', $suffix );
}

if ( ! function_exists( 'yith_ywpar_install_woocommerce_admin_notice' ) ) {
	/**
	 * Admin notice when WooCommerce isn't installed.
	 */
	function yith_ywpar_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH WooCommerce Points and Rewards is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-points-and-rewards' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_ywpar_premium_install' ) ) {
	/**
	 * Check if create the points db table.
	 */
	function yith_ywpar_premium_install() {
		// DO_ACTION : yith_ywpar_init : action triggered before install the plugin.
		do_action( 'yith_ywpar_init' );

		// check for update table.
		if ( function_exists( 'yith_ywpar_update_db_check' ) ) {
			yith_ywpar_update_db_check();
		}
	}

	add_action( 'plugins_loaded', 'yith_ywpar_premium_install', 11 );
}


/**
 * Reset option version
 */
function yith_ywpar_reset_option_version() {
	$old = get_option( 'yit_ywpar_option_version' );
	if ( $old ) {
		add_option( 'yit_ywpar_previous_version', $old );
	} else {
		add_option( 'yit_ywpar_expiration_mode', 'from_1.3.0' );
	}

	delete_option( 'yit_ywpar_option_version' );
}
register_activation_hook( __FILE__, 'yith_ywpar_reset_option_version' );

/**
 * Remove Cron Scheduled Events
 */
register_deactivation_hook( __FILE__, 'ywpar_remove_cron_scheduled' );

/**
 * Remove CRON schedule when the plugin will be deactivates.
 */
function ywpar_remove_cron_scheduled() {
	wp_clear_scheduled_hook( 'ywpar_cron' );
	wp_clear_scheduled_hook( 'ywpar_cron_birthday' );
}

/**
 * Start the game
 */
function yith_ywpar_premium_constructor() {

	// Woocommerce installation check _________________________.
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywpar_install_woocommerce_admin_notice' );
		return;
	}

	// Load ywpar text domain ___________________________________.
	load_plugin_textdomain( 'yith-woocommerce-points-and-rewards', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	require_once YITH_YWPAR_INC . 'functions.yith-wc-points-rewards.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards-admin.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards-frontend.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards-earning.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards-redemption.php';
	require_once YITH_YWPAR_INC . 'class.yith-wc-points-rewards-porting.php';
	require_once YITH_YWPAR_INC . '/widgets/class.yith-wc-points-rewards-widget.php';
	require_once YITH_YWPAR_INC . 'admin/yith-wc-points-rewards-customers-view.php';
	require_once YITH_YWPAR_INC . 'admin/yith-wc-points-rewards-customer-history-view.php';

	if ( class_exists( 'YITH_Vendors' ) ) {
		require_once YITH_YWPAR_INC . 'compatibility/yith-woocommerce-product-vendors.php';
	}

	if ( defined( 'YITH_WCAF_PREMIUM' ) ) {
		require_once YITH_YWPAR_INC . 'compatibility/yith-woocommerce-affiliates.php';
	}

	if ( defined( 'YITH_YWSBS_PREMIUM' ) ) {
		require_once YITH_YWPAR_INC . 'compatibility/yith-woocommerce-subscription.php';
	}

	if ( is_admin() ) {
		YITH_WC_Points_Rewards_Admin();
	}

	YITH_WC_Points_Rewards();
	YITH_WC_Points_Rewards_Earning();
	YITH_WC_Points_Rewards_Redemption();
	YITH_WC_Points_Rewards_Frontend();
}
add_action( 'yith_ywpar_init', 'yith_ywpar_premium_constructor' );
