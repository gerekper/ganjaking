<?php
/**
 * Plugin Name: YITH WooCommerce Custom Order Status Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-custom-order-status/
 * Description: <code><strong>YITH WooCommerce Custom Order Status</strong></code> allows you to create and manage new custom order statuses. For example, you can create "in shipping" or "shipped" before setting orders with those statuses to completed. A big advantage for your internal management. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.27.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-custom-order-status
 * Domain Path: /languages/
 * WC requires at least: 7.9.0
 * WC tested up to: 8.1.x
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Custom Order Status Premium
 * @version 1.27.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCCOS_FREE_INIT', plugin_basename( __FILE__ ) );

/**
 * WooCommerce admin notice.
 */
function yith_wccos_pr_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Custom Order Status Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-custom-order-status' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );


if ( ! defined( 'YITH_WCCOS_VERSION' ) ) {
	define( 'YITH_WCCOS_VERSION', '1.27.0' );
}

if ( ! defined( 'YITH_WCCOS_PREMIUM' ) ) {
	define( 'YITH_WCCOS_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCCOS_INIT' ) ) {
	define( 'YITH_WCCOS_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCOS' ) ) {
	define( 'YITH_WCCOS', true );
}

if ( ! defined( 'YITH_WCCOS_FILE' ) ) {
	define( 'YITH_WCCOS_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCCOS_URL' ) ) {
	define( 'YITH_WCCOS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCOS_DIR' ) ) {
	define( 'YITH_WCCOS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCOS_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCCOS_TEMPLATE_PATH', YITH_WCCOS_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCCOS_ASSETS_URL' ) ) {
	define( 'YITH_WCCOS_ASSETS_URL', YITH_WCCOS_URL . 'assets' );
}

if ( ! defined( 'YITH_WCCOS_ASSETS_PATH' ) ) {
	define( 'YITH_WCCOS_ASSETS_PATH', YITH_WCCOS_DIR . 'assets' );
}

if ( ! defined( 'YITH_WCCOS_SLUG' ) ) {
	define( 'YITH_WCCOS_SLUG', 'yith-woocommerce-custom-order-status' );
}

if ( ! defined( 'YITH_WCCOS_SECRET_KEY' ) ) {
	define( 'YITH_WCCOS_SECRET_KEY', '4yiQOGGPmRNLese2qz0I' );
}

/**
 * Init.
 */
function yith_wccos_pr_init() {
	load_plugin_textdomain( 'yith-woocommerce-custom-order-status', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/class.yith-wccos-admin.php';
	require_once 'includes/class.yith-wccos-frontend.php';
	require_once 'includes/class.yith-wccos.php';
	require_once 'includes/class.yith-wccos-admin-premium.php';
	require_once 'includes/class.yith-wccos-frontend-premium.php';
	require_once 'includes/class.yith-wccos-premium.php';
	require_once 'includes/class.yith-wccos-updates.php';
	require_once 'includes/integrations/class.yith-wccos-integrations.php';
	require_once 'includes/functions.yith-wccos.php';
	require_once 'includes/functions.yith-wccos-colors.php';

	// Let's start the game!
	yith_wccos();
}

add_action( 'yith_wccos_pr_init', 'yith_wccos_pr_init' );

/**
 * Install.
 */
function yith_wccos_pr_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wccos_pr_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wccos_pr_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wccos_pr_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
