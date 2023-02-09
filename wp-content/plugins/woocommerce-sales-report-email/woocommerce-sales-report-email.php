<?php
/**
 * Plugin Name: WooCommerce Sales Report Email
 * Plugin URI: https://woocommerce.com/products/woocommerce-sales-report-email/
 * Description: Daily Sales Report Email
 * Version: 1.2.0
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Text Domain: woocommerce-sales-report-email
 * Domain Path: /languages
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.1
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.3
 * Woo: 473579:a276a32606bc06fc451666a02c52cc64
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-sales-report-email
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Sales_Report_Email\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_SALES_REPORT_EMAIL_FILE' ) ) {
	define( 'WC_SALES_REPORT_EMAIL_FILE', __FILE__ );
}

/**
 * Main Sales Report Email class.
 */
class WooCommerce_Sales_Report_Email extends \Themesquad\WC_Sales_Report_Email\Plugin {

	/**
	 * Get the plugin file
	 *
	 * @since  1.0.0
	 * @return String
	 */
	public static function get_plugin_file() {
		return WC_SALES_REPORT_EMAIL_FILE;
	}

	/**
	 * A static method that will setup the autoloader.
	 *
	 * @since  1.0.0
	 */
	private static function setup_autoloader() {
		require_once plugin_dir_path( self::get_plugin_file() ) . '/classes/class-wc-sre-autoloader.php';
		$autoloader = new WC_SRE_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'classes/' );
		spl_autoload_register( array( $autoloader, 'load' ) );
	}

	/**
	 * This method will be run on plugin activation.
	 *
	 * @since  1.0.0
	 */
	public static function activation() {

		// Setup the autoloader.
		self::setup_autoloader();

		// Setup Cron.
		$cron_manager = new WC_SRE_Cron_Manager();
		$cron_manager->setup_cron();

	}

	/**
	 * This method wil run on plugin deactivation.
	 *
	 * @since  1.0.0
	 */
	public static function deactivation() {

		// Setup the autoloader.
		self::setup_autoloader();

		// Remove Cron.
		$cron_manager = new WC_SRE_Cron_Manager();
		$cron_manager->remove_cron();

	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		parent::__construct();

		// Check if WC is activated.
		if ( $this->is_wc_active() ) {
			$this->init();
		}

	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	private function is_wc_active() {

		$is_active = class_exists( 'WooCommerce' );

		// Do the WC active check.
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}

		return $is_active;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 */
	private function init() {
		// save email settings.
		add_action( 'woocommerce_settings_saved', array( $this, 'reset_cron' ) );

		// Setup the autoloader.
		self::setup_autoloader();

		// Only load in admin.
		if ( is_admin() ) {

			// Setup the settings.
			$settings = new WC_SRE_Settings();
			$settings->setup();

		}

		// Cron hook.
		add_action( WC_SRE_Cron_Manager::CRON_HOOK, array( $this, 'cron_email_callback' ) );

	}

	/**
	 * Method triggered on saving admin sales report email settings.
	 * This is to make sure the time_sent parameter gets changed in the sheduled event.
	 *
	 * @since  1.1.0
	 */
	public function reset_cron() {
		if ( isset( $_REQUEST['woocommerce_sales_report_email_send_time'] ) && strlen( $_REQUEST['woocommerce_sales_report_email_send_time'] > 0 ) ) {
			$cron_manager = new WC_SRE_Cron_Manager();
			$cron_manager->remove_cron();
			$cron_manager->setup_cron();
		}
	}

	/**
	 * Method triggered on Cron run.
	 * This method will create a WC_SRE_Sales_Report_Email object and call trigger method.
	 *
	 * @since  1.0.0
	 */
	public function cron_email_callback() {

		WC()->mailer();

		// This will be a WP Cron action.
		$email = new WC_SRE_Sales_Report_Email();
		$email->trigger();

	}

	/**
	 * Display the WooCommerce activation notice.
	 *
	 * @since  1.0.0
	 */
	public function notice_activate_wc() {
		/* translators: %s: WooCommerce link */
		echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Sales Report Email requires %s to be installed and active.', 'woocommerce-sales-report-email' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
	}
}

/**
 * Initialize plugin.
 */
function woocommerce_sales_report_email_main_init() {
	WooCommerce_Sales_Report_Email::instance();
}

// Create object - Plugin init.
add_action( 'plugins_loaded', 'woocommerce_sales_report_email_main_init' );

// Activation hook.
register_activation_hook( __FILE__, array( 'WooCommerce_Sales_Report_Email', 'activation' ) );

// Deactivation hook.
register_deactivation_hook( __FILE__, array( 'WooCommerce_Sales_Report_Email', 'deactivation' ) );
