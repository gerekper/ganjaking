<?php
/**
 * Plugin Name: YITH WooCommerce Deposits and Down Payments Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-deposits-and-down-payments/
 * Description: <code><strong>YITH WooCommerce Deposits and Down Payments</strong></code> allows your customers to make a deposit for the products they want to purchase and to pay the balance only at a later time, either online or in your shop. Giving your customers the possibility to book a room or to confirm a service on demand, like a party room or the reservation for a tour, has never been so easy. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 1.3.8
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-deposits-and-down-payments
 * Domain Path: /languages/
 * WC requires at least: 4.0
 * WC tested up to: 4.2
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
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

if ( ! defined( 'YITH_WCDP' ) ) {
	define( 'YITH_WCDP', true );
}

if ( ! defined( 'YITH_WCDP_FREE' ) ) {
	define( 'YITH_WCDP_FREE', true );
}

if ( ! defined( 'YITH_WCDP_URL' ) ) {
	define( 'YITH_WCDP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCDP_DIR' ) ) {
	define( 'YITH_WCDP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCDP_INC' ) ) {
	define( 'YITH_WCDP_INC', YITH_WCDP_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCDP_INIT' ) ) {
	define( 'YITH_WCDP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCDP_PREMIUM_INIT' ) ) {
	define( 'YITH_WCDP_PREMIUM_INIT', plugin_basename( __FILE__ ) );
}

if( ! defined( 'YITH_WCDP_SLUG' ) ){
	define( 'YITH_WCDP_SLUG', 'yith-woocommerce-deposits-and-down-payments' );
}

if( ! defined( 'YITH_WCDP_SECRET_KEY' ) ){
	define( 'YITH_WCDP_SECRET_KEY', 'HYbRqbc7fBRGcswTemNi' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCDP_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCDP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCDP_DIR  );

if( ! function_exists( 'yith_deposits_and_down_payments_constructor' ) ) {
	function yith_deposits_and_down_payments_constructor() {
		load_plugin_textdomain( 'yith-woocommerce-deposits-and-down-payments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if( ! class_exists( 'WP_List_Table' ) ){
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		require_once( YITH_WCDP_INC . 'functions.yith-wcdp.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-premium.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-suborders.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-suborders-premium.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-frontend.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-frontend-premium.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-deposits-handler.php' );
		require_once( YITH_WCDP_INC . 'admin-tables/class.yith-wcdp-role-deposits-table.php' );
		require_once( YITH_WCDP_INC . 'admin-tables/class.yith-wcdp-product-deposits-table.php' );
		require_once( YITH_WCDP_INC . 'admin-tables/class.yith-wcdp-category-deposits-table.php' );
		require_once( YITH_WCDP_INC . 'compatibility/class.yith-wcdp-compatibility.php' );
		require_once( YITH_WCDP_INC . 'class.yith-wcdp-support-cart.php' );
        require_once( YITH_WCDP_INC . 'class.yith-wcdp-shortcode.php' );


        // Let's start the game
		YITH_WCDP_Premium();

		if( is_admin() ){
			require_once( YITH_WCDP_INC . 'class.yith-wcdp-admin.php' );
			require_once( YITH_WCDP_INC . 'class.yith-wcdp-admin-premium.php' );

			YITH_WCDP_Admin_Premium();
		}
	}
}
add_action( 'yith_wcdp_init', 'yith_deposits_and_down_payments_constructor' );

if( ! function_exists( 'yith_deposits_and_down_payments_install' ) ) {
	function yith_deposits_and_down_payments_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'yit_deactive_free_version' ) ) {
			require_once 'plugin-fw/yit-deactive-plugin.php';
		}
		yit_deactive_free_version( 'YITH_WCDP_FREE_INIT', plugin_basename( __FILE__ ) );

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcdp_install_woocommerce_admin_notice' );
		}
		else {
			do_action( 'yith_wcdp_init' );
		}
	}
}
add_action( 'plugins_loaded', 'yith_deposits_and_down_payments_install', 11 );

if( ! function_exists( 'yith_wcdp_install_woocommerce_admin_notice' ) ) {
	function yith_wcdp_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php echo sprintf( __( '%s is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-deposits-and-down-payments' ), 'YITH WooCommerce Deposits and Down Payments' ); ?></p>
		</div>
		<?php
	}
}

if( ! function_exists( 'yith_wcdp_install_free_admin_notice' ) ){
	function yith_wcdp_install_free_admin_notice() {
		?>
		<div class="error">
			<p><?php echo sprintf( __( 'You can\'t activate the free version of %s while you are using the premium one.', 'yith-woocommerce-deposits-and-down-payments' ), 'YITH WooCommerce Deposits and Down Payments' ); ?></p>
		</div>
		<?php
	}
}