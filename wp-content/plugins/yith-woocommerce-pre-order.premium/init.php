<?php
/*
Plugin Name: YITH Pre-Order for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-pre-order
Description: Thanks to <code><strong>YITH Pre-Order for WooCommerce Premium</strong></code> you can improve right away the sales of unavailable items, offering your customers the chance to purchase the products and receive them only after they are officially on sale. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
Version: 1.5.12
Author: YITH
Author URI: https://yithemes.com/
Text Domain: yith-pre-order-for-woocommerce
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 4.2
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

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCPO_FREE_INIT', plugin_basename( __FILE__ ) );

/* === DEFINE === */
! defined( 'YITH_WCPO_VERSION' ) && define( 'YITH_WCPO_VERSION', '1.5.12' );
! defined( 'YITH_WCPO_INIT' ) && define( 'YITH_WCPO_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCPO_SLUG' ) && define( 'YITH_WCPO_SLUG', 'yith-woocommerce-pre-order' );
! defined( 'YITH_WCPO_SECRETKEY' ) && define( 'YITH_WCPO_SECRETKEY', 'IuVtJPflp0YXWOXGW0uW' );
! defined( 'YITH_WCPO_FILE' ) && define( 'YITH_WCPO_FILE', __FILE__ );
! defined( 'YITH_WCPO_PATH' ) && define( 'YITH_WCPO_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCPO_URL' ) && define( 'YITH_WCPO_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCPO_ASSETS_URL' ) && define( 'YITH_WCPO_ASSETS_URL', YITH_WCPO_URL . 'assets/' );
! defined( 'YITH_WCPO_ASSETS_JS_URL' ) && define( 'YITH_WCPO_ASSETS_JS_URL', YITH_WCPO_URL . 'assets/js/' );
! defined( 'YITH_WCPO_TEMPLATE_PATH' ) && define( 'YITH_WCPO_TEMPLATE_PATH', YITH_WCPO_PATH . 'templates/' );
! defined( 'YITH_WCPO_WC_TEMPLATE_PATH' ) && define( 'YITH_WCPO_WC_TEMPLATE_PATH', YITH_WCPO_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCPO_OPTIONS_PATH' ) && define( 'YITH_WCPO_OPTIONS_PATH', YITH_WCPO_PATH . 'plugin-options' );
! defined( 'YITH_WCPO_PREMIUM' ) && define( 'YITH_WCPO_PREMIUM', '1' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCPO_PATH . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCPO_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCPO_PATH );

/* Register the Pre-Order notifications Cron Jobs */
function start_preorder_date_notification_scheduling() {
	wp_schedule_event( time(), 'daily', 'ywpo_preorder_date_notification' );
}
function start_preorder_date_end_check_scheduling() {
	wp_schedule_event( time(), 'twicedaily', 'ywpo_preorder_date_end_check' );
}

function end_preorder_date_notification_scheduling() {
	wp_clear_scheduled_hook( 'ywpo_preorder_date_notification' );
}
function end_preorder_date_end_check_scheduling() {
	wp_clear_scheduled_hook( 'ywpo_preorder_date_end_check' );
}
function end_preorder_is_for_sale_single_notification_scheduling() {
	wp_clear_scheduled_hook( 'ywpo_preorder_is_for_sale_single_notification' );
}

register_activation_hook( YITH_WCPO_FILE, 'start_preorder_date_notification_scheduling' );
register_activation_hook( YITH_WCPO_FILE, 'start_preorder_date_end_check_scheduling' );
register_deactivation_hook( YITH_WCPO_FILE, 'end_preorder_date_notification_scheduling' );
register_deactivation_hook( YITH_WCPO_FILE, 'end_preorder_date_end_check_scheduling' );
register_deactivation_hook( YITH_WCPO_FILE, 'end_preorder_is_for_sale_single_notification_scheduling' );

// Register 'My Pre-Orders'(From 'My Account' page) endpoint.
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/* Start the plugin on plugins_loaded */

if ( ! function_exists( 'yith_ywpo_install' ) ) {
	/**
	 * Install the plugin
	 */
	function yith_ywpo_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywpo_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywpo_init' );
		}
	}
	add_action( 'plugins_loaded', 'yith_ywpo_install', 11 );
}

if ( ! function_exists( 'yith_ywpo_install_woocommerce_admin_notice' ) ) {

	function yith_ywpo_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH Pre Order for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-pre-order-for-woocommerce' ); ?></p>
		</div>
		<?php
	}
}

add_action( 'yith_ywpo_init', 'yith_ywpo_init' );

if ( ! function_exists( 'yith_ywpo_init' ) ) {
	/**
	 * Start the plugin
	 */
	function yith_ywpo_init() {
		/**
		 * Load text domain
		 */
		load_plugin_textdomain( 'yith-pre-order-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


		if ( ! function_exists( 'YITH_Pre_Order' ) ) {
			/**
			 * Unique access to instance of YITH_Pre_Order class
			 *
			 * @return YITH_Pre_Order
			 * @since 1.0.0
			 */
			function YITH_Pre_Order() {
				require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order.php' );
				if ( defined( 'YITH_WCPO_PREMIUM' ) && file_exists( YITH_WCPO_PATH . 'includes/class.yith-pre-order-premium.php' ) ) {

					require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-premium.php' );
					return YITH_Pre_Order_Premium::instance();
				}
				return YITH_Pre_Order::instance();
			}
		}

		// Let's start the game!
		YITH_Pre_Order();
	}
}
