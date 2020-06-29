<?php
/**
 * Plugin Name: YITH WooCommerce Recover Abandoned Cart Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-recover-abandoned-cart/
 * Description: <code><strong>YITH WooCommerce Recover Abandoned Cart</strong></code> reminds users who did not complete the checkout about their pending order, so you can recover this lost sale. Recovering abandoned carts increase dramatically the conversion rate of your e-commerce shop. It's perfect if you want to maximise profit. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.4.4
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-recover-abandoned-cart
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0
 **/


/*
 * @package YITH WooCommerce Recover Abandoned Cart Premium
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}




if ( ! defined( 'YITH_YWRAC_DIR' ) ) {
	define( 'YITH_YWRAC_DIR', plugin_dir_path( __FILE__ ) );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWRAC_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWRAC_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWRAC_DIR );



// Define constants ________________________________________
if ( defined( 'YITH_YWRAC_VERSION' ) ) {
	return;
} else {
	define( 'YITH_YWRAC_VERSION', '1.4.4' );
}

if ( ! defined( 'YITH_YWRAC_PREMIUM' ) ) {
	define( 'YITH_YWRAC_PREMIUM', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_INIT' ) ) {
	define( 'YITH_YWRAC_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_FILE' ) ) {
	define( 'YITH_YWRAC_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWRAC_URL' ) ) {
	define( 'YITH_YWRAC_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_ASSETS_URL' ) ) {
	define( 'YITH_YWRAC_ASSETS_URL', YITH_YWRAC_URL . 'assets' );
}

if ( ! defined( 'YITH_YWRAC_TEMPLATE_PATH' ) ) {
	define( 'YITH_YWRAC_TEMPLATE_PATH', YITH_YWRAC_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWRAC_INC' ) ) {
	define( 'YITH_YWRAC_INC', YITH_YWRAC_DIR . '/includes/' );
}

if ( ! defined( 'YITH_YWRAC_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWRAC_SUFFIX', $suffix );
}

if ( ! defined( 'YITH_YWRAC_SLUG' ) ) {
	define( 'YITH_YWRAC_SLUG', 'yith-woocommerce-recover-abandoned-cart' );
}

if ( ! defined( 'YITH_YWRAC_SECRET_KEY' ) ) {
	define( 'YITH_YWRAC_SECRET_KEY', 'EMqDs75CCAYPHZVZcFna' );
}

// Free version deactivation if installed __________________
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWRAC_FREE_INIT', plugin_basename( __FILE__ ) );


if ( ! function_exists( 'yith_ywrac_install_woocommerce_admin_notice' ) ) {
	function yith_ywrac_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _ex( 'YITH WooCommerce Recover Abandoned Cart is enabled but not effective. It requires WooCommerce in order to work.', 'do not translate plugin name', 'yith-woocommerce-recover-abandoned-cart' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'ywrac_plugin_registration_hook' ) ) {
	function ywrac_plugin_registration_hook() {
		if ( get_option( 'ywrac_pending_orders_enabled', 'no' ) != 'yes' ) {
			return;
		}

		$pending_orders = get_option( 'ywrac_total_pending_orders' );
		if ( $pending_orders ) {
			return;
		}

		$cart_counter = intval( get_option( 'ywrac_abandoned_carts_counter', 0 ) );

		global $wpdb;
		$query = "SELECT count( ywrac_p.ID ) FROM $wpdb->posts as ywrac_p
            WHERE ywrac_p.post_type = 'shop_order'
            AND ywrac_p.post_status = 'wc-pending'";
		$count = $wpdb->get_var( $query );

		if ( $count ) {
			add_option( 'ywrac_total_pending_orders', $count );
			update_option( 'ywrac_abandoned_carts_counter', $cart_counter + $count );
		}
	}
}
register_activation_hook( __FILE__, 'ywrac_plugin_registration_hook' );


if ( ! function_exists( 'yith_ywrac_install' ) ) {
	function yith_ywrac_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywrac_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywrac_init' );
		}

		// check for update table
		if ( function_exists( 'yith_ywrac_update_db_check' ) ) {
			yith_ywrac_update_db_check();
		}
	}

	add_action( 'plugins_loaded', 'yith_ywrac_install', 11 );
}


function yith_ywrac_premium_constructor() {

	require_once YITH_YWRAC_INC . 'functions.yith-wc-abandoned-cart.php';

	// WooCommerce installation check _________________________
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywrac_install_woocommerce_admin_notice' );
		return;
	}

	// Load YWRAC text domain ___________________________________
	load_plugin_textdomain( 'yith-woocommerce-recover-abandoned-cart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-unsubscribe.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-email.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-helper.php';
	require_once YITH_YWRAC_INC . 'class.yith-wc-abandoned-cart-privacy.php';

	require_once YITH_YWRAC_INC . 'admin/class-wp-carts-list-table.php';
	require_once YITH_YWRAC_INC . 'admin/class-wp-pending-orders-list-table.php';
	require_once YITH_YWRAC_INC . 'admin/class-wp-emails-list-table.php';
	require_once YITH_YWRAC_INC . 'admin/class-wp-email-log-list-table.php';
	require_once YITH_YWRAC_INC . 'admin/class-wp-recovered-list-table.php';

	YITH_WC_Recover_Abandoned_Cart();
	YITH_WC_Recover_Abandoned_Cart_Email();

	if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend' ) ) {

		require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-admin.php';
		require_once YITH_YWRAC_INC . 'admin/class-yith-wc-abandoned-cart-metaboxes.php';

		YITH_WC_Recover_Abandoned_Cart_Admin();

	}

	YITH_WC_Recover_Abandoned_Cart_Helper();

	add_action( 'ywrac_cron', array( YITH_WC_Recover_Abandoned_Cart_Email(), 'email_cron' ) );
	add_action( 'ywrac_cron', array( YITH_WC_Recover_Abandoned_Cart_Helper(), 'clear_coupons' ) );

}
add_action( 'yith_ywrac_init', 'yith_ywrac_premium_constructor' );
