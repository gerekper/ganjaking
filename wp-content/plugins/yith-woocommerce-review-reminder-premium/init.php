<?php
/**
 * Plugin Name: YITH WooCommerce Review Reminder Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-review-reminder
 * Description: <code><strong>YITH WooCommerce Review Reminder</strong></code> allows you to increase the number of quality reviews for the products of your store, by automatically reminding users of adding one. Choose your best communication strategy to encourage them to express their opinion and you'll see your store grow every day more! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-woocommerce-review-reminder
 * Version: 1.34.0
 * Author URI: https://yithemes.com/
 * WC requires at least: 7.9.0
 * WC tested up to: 8.1.x
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Show error message if WooCommerce is disabled
 *
 * @return  void
 * @since   1.0.0
 */
function ywrr_install_premium_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p>
			<?php
			/* translators: %s name of the plugin */
			echo sprintf( esc_html__( '%s is enabled but not effective. In order to work, it requires WooCommerce.', 'yith-woocommerce-review-reminder' ), 'YITH WooCommerce Review Reminder' );
			?>
		</p>
	</div>
	<?php
}

if ( ! defined( 'YWRR_VERSION' ) ) {
	define( 'YWRR_VERSION', '1.34.0' );
}

if ( ! defined( 'YWRR_DB_VERSION' ) ) {
	define( 'YWRR_DB_VERSION', '1.13.0' );
}

if ( ! defined( 'YWRR_DB_VERSION_EXT' ) ) {
	define( 'YWRR_DB_VERSION_EXT', '1.13.0' );
}

if ( ! defined( 'YWRR_INIT' ) ) {
	define( 'YWRR_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWRR_SLUG' ) ) {
	define( 'YWRR_SLUG', 'yith-woocommerce-review-reminder' );
}

if ( ! defined( 'YWRR_SECRET_KEY' ) ) {
	define( 'YWRR_SECRET_KEY', 'LDgCfgh9GZCnoX6UjYzI' );
}

if ( ! defined( 'YWRR_PREMIUM' ) ) {
	define( 'YWRR_PREMIUM', '1' );
}

if ( ! defined( 'YWRR_FILE' ) ) {
	define( 'YWRR_FILE', __FILE__ );
}

if ( ! defined( 'YWRR_DIR' ) ) {
	define( 'YWRR_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWRR_URL' ) ) {
	define( 'YWRR_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWRR_ASSETS_URL' ) ) {
	define( 'YWRR_ASSETS_URL', YWRR_URL . 'assets/' );
}

if ( ! defined( 'YWRR_TEMPLATE_PATH' ) ) {
	define( 'YWRR_TEMPLATE_PATH', YWRR_DIR . 'templates/' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWRR_DIR . 'plugin-fw/init.php' ) ) {
	require_once YWRR_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YWRR_DIR );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

/**
 * Run plugin
 *
 * @return  void
 * @since   1.0.0
 */
function ywrr_premium_init() {

	/* Load YWRR text domain */
	load_plugin_textdomain( 'yith-woocommerce-review-reminder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	YITH_WRR();

}

add_action( 'ywrr_premium_init', 'ywrr_premium_init' );

/**
 * Initialize plugin
 *
 * @return void
 * @since   1.0.0
 */
function ywrr_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ywrr_install_premium_woocommerce_admin_notice' );
	} else {
		do_action( 'ywrr_premium_init' );
		ywrr_create_tables();
	}
}

add_action( 'plugins_loaded', 'ywrr_premium_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'YITH_WRR' ) ) {

	/**
	 * Unique access to instance of YWRR_Review_Reminder
	 *
	 * @return  YWRR_Review_Reminder
	 * @since   1.1.5
	 */
	function YITH_WRR() { //phpcs:ignore

		// Load required classes and functions.
		require_once YWRR_DIR . 'class-yith-woocommerce-review-reminder.php';

		return YWRR_Review_Reminder::get_instance();

	}
}


register_activation_hook( __FILE__, 'ywrr_create_tables' );
register_deactivation_hook( __FILE__, 'ywrr_create_unschedule_job' );

if ( ! function_exists( 'ywrr_create_tables' ) ) {

	/**
	 * Creates database table for blocklist and scheduling
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	function ywrr_create_tables() {

		$current_version = get_option( 'ywrr_db_version' );

		if ( ( YWRR_DB_VERSION !== $current_version ) || ! empty( $_GET['ywrr_force_create_table'] ) ) { //phpcs:ignore

			global $wpdb;

			$wpdb->hide_errors();

			$collate = $wpdb->get_charset_collate();

			$blocklist_table_name = $wpdb->prefix . 'ywrr_email_blocklist';
			$schedule_table_name  = $wpdb->prefix . 'ywrr_email_schedule';

			$blocklist_table_sql = "
            CREATE TABLE $blocklist_table_name (
              id				int 		NOT NULL AUTO_INCREMENT,
              customer_email	longtext 	NOT NULL,
              customer_id		bigint(20)	NOT NULL DEFAULT 0,
              PRIMARY KEY (id)
            ) $collate;";

			$schedule_table_sql = "
            CREATE TABLE $schedule_table_name (
              id 				int 			NOT NULL AUTO_INCREMENT,
              order_id 			bigint(20)		NOT NULL,
              order_date 		date			NOT NULL DEFAULT '0000-00-00',
              scheduled_date	date			NOT NULL DEFAULT '0000-00-00',
              request_items 	longtext		NOT NULL DEFAULT '',
              mail_status 		varchar(15)		NOT NULL DEFAULT 'pending',
              mail_type 		varchar(100)	NOT NULL DEFAULT 'order',
              PRIMARY KEY (id)
            ) $collate;";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			dbDelta( $blocklist_table_sql );
			dbDelta( $schedule_table_sql );

			update_option( 'ywrr_db_version', YWRR_DB_VERSION );
			update_option( 'ywrr_db_version_ext', YWRR_DB_VERSION_EXT );

		}

	}
}

if ( ! function_exists( 'ywrr_create_unschedule_job' ) ) {

	/**
	 * Removes cron job
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	function ywrr_create_unschedule_job() {
		wp_clear_scheduled_hook( 'ywrr_daily_send_mail_job' );
		wp_clear_scheduled_hook( 'ywrr_hourly_send_mail_job' );
	}
}


add_action( 'before_woocommerce_init', 'ywrr_declare_hpos_compatibility' );

/**
 * Declare HPOS compatibility
 *
 * @return void
 * @since  1.28.0
 */
function ywrr_declare_hpos_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
