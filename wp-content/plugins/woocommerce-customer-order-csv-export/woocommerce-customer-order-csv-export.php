<?php
/**
 * Plugin Name: WooCommerce Customer/Order/Coupon Export
 * Plugin URI: http://www.woocommerce.com/products/ordercustomer-csv-export/
 * Documentation URI: https://docs.woocommerce.com/document/ordercustomer-csv-export/
 * Description: Easily download customers, orders, and coupons in CSV and XML and schedule recurring, automated exports via FTP, HTTP POST, and more.
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 5.5.0
 * Text Domain: woocommerce-customer-order-csv-export
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2015-2023, SkyVerge (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Customer-Order-CSV-Export
 * @author    SkyVerge
 * @category  Export
 * @copyright Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 18652:914de15813a903c767b55445608bf290
 * WC requires at least: 3.9.4
 * WC tested up to: 7.9.0
 */

defined( 'ABSPATH' ) or exit;

// required Action Scheduler library
// TODO: we can stop bundling Action Scheduler when WooCommerce 4.0 is the minimum required version as it's now part of core WooCommerce {WV 2019-11-12}
require_once( plugin_dir_path( __FILE__ ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php' );

/**
 * The plugin loader class.
 *
 * @since 4.7.0
 */
class WC_Customer_Order_CSV_Export_Loader {


	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.4';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.6';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.9.4';

	/** SkyVerge plugin framework version used by this plugin */
	const FRAMEWORK_VERSION = '5.11.6';

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'WooCommerce Customer/Order/Coupon Export';


	/** @var WC_Customer_Order_CSV_Export_Loader single instance of this class */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = [];


	/**
	 * Constructs the class.
	 *
	 * @since 4.7.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, [ $this, 'activation_check' ] );

		add_action( 'admin_init', [ $this, 'check_environment' ] );
		add_action( 'admin_init', [ $this, 'add_plugin_notices' ] );

		add_action( 'admin_notices', [ $this, 'admin_notices' ], 15 );

		add_filter( 'extra_plugin_headers', array( $this, 'add_documentation_header' ) );

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {

			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 4.7.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '4.7.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 4.7.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '4.7.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 4.7.0
	 */
	public function init_plugin() {

		if ( ! $this->plugins_compatible() ) {
			return;
		}

		$this->load_framework();

		// load the main plugin class
		require_once( plugin_dir_path( __FILE__ ) . 'class-wc-customer-order-csv-export.php' );

		// fire it up!
		wc_customer_order_csv_export();
	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 4.7.0
	 */
	protected function load_framework() {

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Plugin' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php' );
		}
	}


	/**
	 * Gets the framework version in namespace form.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	protected function get_framework_version_namespace() {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Gets the framework version used by this plugin.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	protected function get_framework_version() {

		return self::FRAMEWORK_VERSION;
	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @since 4.7.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 4.7.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @since 4.7.0
	 */
	public function add_plugin_notices() {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'
			) );
		}
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		if ( ! self::MINIMUM_WP_VERSION ) {
			return true;
		}

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		if ( ! self::MINIMUM_WC_VERSION ) {
			return true;
		}

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 4.7.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 4.7.0
	 *
	 * @param string $slug the slug for the notice
	 * @param string $class the css class for the notice
	 * @param string $message the notice message
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = [
			'class'   => $class,
			'message' => $message
		];
	}


	/**
	 * Displays any admin notices added with \WC_Customer_Order_CSV_Export_Loader::add_admin_notice()
	 *
	 * @since 4.7.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) {

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p>
					<?php echo wp_kses( $notice['message'], [ 'a' => [ 'href' => [] ] ] ); ?>
				</p>
			</div>
			<?php
		}
	}


	/**
	 * Adds the Documentation URI header.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 *
	 * @param string[] $headers original headers
	 * @return string[]
	 */
	public function add_documentation_header( $headers ) {

		$headers[] = 'Documentation URI';

		return $headers;
	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		$message = sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );

		return $message;
	}


	/**
	 * Gets the main \WC_Customer_Order_CSV_Export_Loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 4.7.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

// fire it up!
WC_Customer_Order_CSV_Export_Loader::instance();
