<?php
/**
 * Plugin Name: YITH WooCommerce Affiliates Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-affiliates/
 * Description: <code><strong>YITH WooCommerce Affiliates</strong></code> allows your users to become affiliates on your site earning commissions on every sale generated through their exclusive affiliation links. Create a sales network at no cost and increase your incomes just like big stores. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 1.7.2
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-affiliates
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.1
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.2.3
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCAF' ) ) {
	define( 'YITH_WCAF', true );
}

if ( ! defined( 'YITH_WCAF_PREMIUM' ) ) {
	define( 'YITH_WCAF_PREMIUM', true );
}

if ( ! defined( 'YITH_WCAF_URL' ) ) {
	define( 'YITH_WCAF_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAF_DIR' ) ) {
	define( 'YITH_WCAF_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAF_INC' ) ) {
	define( 'YITH_WCAF_INC', YITH_WCAF_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCAF_INIT' ) ) {
	define( 'YITH_WCAF_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAF_PREMIUM_INIT' ) ) {
	define( 'YITH_WCAF_PREMIUM_INIT', plugin_basename( __FILE__ ) );
}

if( ! defined( 'YITH_WCAF_SLUG' ) ){
	define( 'YITH_WCAF_SLUG', 'yith-woocommerce-affiliates' );
}

if( ! defined( 'YITH_WCAF_SECRET_KEY' ) ){
	define( 'YITH_WCAF_SECRET_KEY', '12345' );
}

$wp_upload_dir = wp_upload_dir();

if( ! defined( 'YITH_WCAF_INVOICES_DIR' ) ){
    define( 'YITH_WCAF_INVOICES_DIR', $wp_upload_dir[ 'basedir' ] . '/yith-wcaf-invoices/' );
}

if( ! defined( 'YITH_WCAF_INVOICES_URL' ) ){
    define( 'YITH_WCAF_INVOICES_URL', $wp_upload_dir[ 'baseurl' ] . '/yith-wcaf-invoices/' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCAF_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCAF_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCAF_DIR  );

if( ! function_exists( 'yith_affiliates_constructor' ) ) {
	function yith_affiliates_constructor() {
		load_plugin_textdomain( 'yith-woocommerce-affiliates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( YITH_WCAF_INC . 'functions.yith-wcaf.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-shortcode.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-shortcode-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-click-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-click-handler-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-commission-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-commission-handler-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-coupon-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-rate-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-rate-handler-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-affiliate-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-affiliate-handler-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-payment-handler.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-payment-handler-premium.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-affiliate.php' );
		require_once( YITH_WCAF_INC . 'class.yith-wcaf-affiliate-premium.php' );

		// Let's start the game
		YITH_WCAF_Premium();

		if( is_admin() ){
			if( ! class_exists( 'WP_List_Table' ) ){
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-user-rates-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-product-rates-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-clicks-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-commissions-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-commissions-table-premium.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-payments-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-payments-table-premium.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-affiliates-table.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-affiliates-table-premium.php' );
			require_once( YITH_WCAF_INC . 'admin-tables/class.yith-wcaf-product-stat-table.php' );
			require_once( YITH_WCAF_INC . 'class.yith-wcaf-admin.php' );
			require_once( YITH_WCAF_INC . 'class.yith-wcaf-admin-premium.php' );

			YITH_WCAF_Admin_Premium();
		}
	}
}
add_action( 'yith_wcaf_init', 'yith_affiliates_constructor' );

if( ! function_exists( 'yith_affiliates_install' ) ) {
	function yith_affiliates_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'yit_deactive_free_version' ) ) {
			require_once 'plugin-fw/yit-deactive-plugin.php';
		}
		yit_deactive_free_version( 'YITH_WCAF_FREE_INIT', plugin_basename( __FILE__ ) );

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcaf_install_woocommerce_admin_notice' );
		}
		else {
			do_action( 'yith_wcaf_init' );
		}
	}
}
add_action( 'plugins_loaded', 'yith_affiliates_install', 11 );

if( ! function_exists( 'yith_wcaf_install_woocommerce_admin_notice' ) ) {
	function yith_wcaf_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php echo 'YITH WooCommerce Affiliates ' . __( 'is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-affiliates' ); ?></p>
		</div>
	<?php
	}
}

if( ! function_exists( 'yith_wcaf_install_free_admin_notice' ) ){
	function yith_wcaf_install_free_admin_notice() {
		?>
		<div class="error">
			<p><?php echo __( 'You can\'t activate the free version of ', 'yith-woocommerce-affiliates' ) . 'YITH WooCommerce Affiliates' . __( ' while you are using the premium one.', 'yith-woocommerce-affiliates' ); ?></p>
		</div>
	<?php
	}
}
