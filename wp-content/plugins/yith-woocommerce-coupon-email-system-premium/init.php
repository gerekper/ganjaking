<?php
/**
 * Plugin Name: YITH WooCommerce Coupon Email System Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-coupon-email-system/
 * Description: <code><strong>YITH WooCommerce Coupon Email System</strong></code> allows sending one or more coupons based on specific rules to your customers automatically. You could, for example, send a coupon code to all customers who register to your store or to those who reach a certain number of orders in your shop and so on. It's perfect to encourage your users to buy again and again from you. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-woocommerce-coupon-email-system
 * Version: 1.4.5
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

function ywces_install_woocommerce_premium_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Coupon Email System is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-coupon-email-system' ); ?></p>
	</div>
	<?php
}

if ( ! defined( 'YWCES_VERSION' ) ) {
	define( 'YWCES_VERSION', '1.4.5' );
}

if ( ! defined( 'YWCES_INIT' ) ) {
	define( 'YWCES_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWCES_SLUG' ) ) {
	define( 'YWCES_SLUG', 'yith-woocommerce-coupon-email-system' );
}

if ( ! defined( 'YWCES_SECRET_KEY' ) ) {
	define( 'YWCES_SECRET_KEY', 'CNXB489Tb1oktD6rIx99' );
}

if ( ! defined( 'YWCES_PREMIUM' ) ) {
	define( 'YWCES_PREMIUM', '1' );
}

if ( ! defined( 'YWCES_FILE' ) ) {
	define( 'YWCES_FILE', __FILE__ );
}

if ( ! defined( 'YWCES_DIR' ) ) {
	define( 'YWCES_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWCES_URL' ) ) {
	define( 'YWCES_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWCES_ASSETS_URL' ) ) {
	define( 'YWCES_ASSETS_URL', YWCES_URL . 'assets' );
}

if ( ! defined( 'YWCES_TEMPLATE_PATH' ) ) {
	define( 'YWCES_TEMPLATE_PATH', YWCES_DIR . 'templates' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCES_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWCES_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YWCES_DIR );

function ywces_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-woocommerce-coupon-email-system', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* === Global YITH WooCommerce Coupon Email System  === */
	YITH_WCES();

}

add_action( 'ywces_init', 'ywces_init' );

function ywces_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ywces_install_woocommerce_premium_admin_notice' );
	} else {
		do_action( 'ywces_init' );
	}

}

add_action( 'plugins_loaded', 'ywces_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );
register_activation_hook( __FILE__, 'ywces_create_schedule_job' );
register_activation_hook( __FILE__, 'ywces_trash_coupon_schedule' );
register_deactivation_hook( __FILE__, 'ywces_create_unschedule_job' );

if ( ! function_exists( 'YITH_WCES' ) ) {

	/**
	 * Unique access to instance of YITH_WC_Coupon_Email_System
	 *
	 * @return  YITH_WC_Coupon_Email_System
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 */
	function YITH_WCES() {

		// Load required classes and functions
		require_once( YWCES_DIR . 'class.yith-wc-coupon-email-system.php' );

		return YITH_WC_Coupon_Email_System::get_instance();

	}

}

if ( ! function_exists( 'ywces_create_schedule_job' ) ) {

	/**
	 * Creates a cron job to handle daily mail send
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 */
	function ywces_create_schedule_job() {

		$ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
		wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'daily', 'ywces_daily_send_mail_job' );

	}

}

if ( ! function_exists( 'ywces_create_unschedule_job' ) ) {

	/**
	 * Removes cron job
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 */
	function ywces_create_unschedule_job() {

		wp_clear_scheduled_hook( 'ywces_daily_send_mail_job' );
		wp_clear_scheduled_hook( 'ywces_trash_coupon_cron' );

	}

}

if ( ! function_exists( 'ywces_trash_coupon_schedule' ) ) {

	/**
	 * Creates a cron job to handle daily expired coupon trash
	 *
	 * @return  void
	 * @since   1.0.5
	 * @author  Alberto Ruggiero
	 */
	function ywces_trash_coupon_schedule() {
		$ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
		wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'daily', 'ywces_trash_coupon_cron' );
	}

}

