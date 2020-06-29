<?php
/**
 * Plugin Name: YITH WooCommerce Anti-Fraud Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-anti-fraud/
 * Description: <code><strong>YITH WooCommerce Anti-Fraud</strong></code> allows increasing the security level of your orders and prevent purchases that match with some specific conditions, such as multiple purchases coming from the same IP address or purchases with too high an amount and so on. Learn more about how to keep safe from frauds. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-woocommerce-anti-fraud
 * Version: 1.3.0
 * Author URI: https://yithemes.com/
 * WC requires at least: 3.9.0
 * WC tested up to: 4.1.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function ywaf_install_woocommerce_premium_admin_notice() {
	?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Anti-Fraud is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-anti-fraud' ); ?></p>
    </div>
	<?php
}

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}

yit_deactive_free_version( 'YWAF_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! defined( 'YWAF_VERSION' ) ) {
	define( 'YWAF_VERSION', '1.3.0' );
}

if ( ! defined( 'YWAF_INIT' ) ) {
	define( 'YWAF_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWAF_SLUG' ) ) {
	define( 'YWAF_SLUG', 'yith-woocommerce-anti-fraud' );
}

if ( ! defined( 'YWAF_SECRET_KEY' ) ) {
	define( 'YWAF_SECRET_KEY', 'kKhx3lign3sonFKLhB9y' );
}

if ( ! defined( 'YWAF_PREMIUM' ) ) {
	define( 'YWAF_PREMIUM', '1' );
}

if ( ! defined( 'YWAF_FILE' ) ) {
	define( 'YWAF_FILE', __FILE__ );
}

if ( ! defined( 'YWAF_DIR' ) ) {
	define( 'YWAF_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWAF_URL' ) ) {
	define( 'YWAF_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWAF_ASSETS_URL' ) ) {
	define( 'YWAF_ASSETS_URL', YWAF_URL . 'assets' );
}

if ( ! defined( 'YWAF_TEMPLATE_PATH' ) ) {
	define( 'YWAF_TEMPLATE_PATH', YWAF_DIR . 'templates' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWAF_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWAF_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YWAF_DIR );

function ywaf_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-woocommerce-anti-fraud', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* === Global YITH WooCommerce Anti-Fraud  === */
	YITH_WAF();

}

add_action( 'ywaf_init', 'ywaf_init' );

function ywaf_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ywaf_install_woocommerce_premium_admin_notice' );
	} else {
		do_action( 'ywaf_init' );
	}

}

add_action( 'plugins_loaded', 'ywaf_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'YITH_WAF' ) ) {

	/**
	 * Unique access to instance of YITH_WC_Anti_Fraud
	 *
	 * @since   1.0.0
	 * @return  YITH_WC_Anti_Fraud
	 * @author  Alberto Ruggiero
	 */
	function YITH_WAF() {

		// Load required classes and functions
		require_once( YWAF_DIR . 'class.yith-wc-anti-fraud.php' );

		return YITH_WC_Anti_Fraud::get_instance();

	}

}

if ( ! function_exists( 'ywaf_cron_schedule' ) ) {

	add_filter( 'cron_schedules', 'ywaf_cron_schedule', 50 );

	function ywaf_cron_schedule( $schedules ) {

		$schedules['ywaf_cron'] = array(
			'interval' => 5 * 60,
			'display'  => __( 'Once every 5 minutes', 'yith-woocommerce-anti-fraud' )
		);

		return $schedules;
	}

}

if ( ! function_exists( 'ywaf_paypal_schedule' ) ) {

	/**
	 * Creates a cron job to handle daily mail send
	 *
	 * @since   1.0.0
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ywaf_paypal_schedule() {
		wp_schedule_event( time(), 'daily', 'ywaf_paypal_cron' );
		wp_schedule_event( time(), 'ywaf_cron', 'ywaf_paypal_data_cron' );
	}

}
register_activation_hook( __FILE__, 'ywaf_paypal_schedule' );

if ( ! function_exists( 'ywaf_paypal_unschedule' ) ) {

	/**
	 * Removes cron job
	 *
	 * @since   1.0.0
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ywaf_paypal_unschedule() {
		wp_clear_scheduled_hook( 'ywaf_paypal_cron' );
		wp_clear_scheduled_hook( 'ywaf_paypal_data_cron' );
	}

}
register_deactivation_hook( __FILE__, 'ywaf_paypal_unschedule' );
