<?php
/**
 * WooCommerce Authorize.Net Reporting
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Reporting to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Reporting for your
 * needs please refer to http://www.skyverge.com/contact/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Authorize.Net Reporting main plugin class.
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.11.0';

	/** @var \WC_Authorize_Net_Reporting single instance of this plugin */
	protected static $instance;

	/** plugin ID */
	const PLUGIN_ID = 'authorize_net_reporting';

	/** @var \WC_Authorize_Net_Reporting_Admin instance */
	protected $admin;

	/** @var \WC_Authorize_Net_Reporting_API instance */
	private $api;

	/** @var null|string temporary export filename, for internal use */
	public $temp_export_file;


	/**
	 * Sets up the plugin.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-authorize-net-reporting',
				'dependencies' => array(
					'php_extensions' => array(
						'dom',
					),
				),
			)
		);

		// include required files
		$this->includes();

		// schedule export + email event
		add_action( 'init', array( $this, 'schedule_export' ) );

		// trigger export + email event
		add_action( 'wc_authorize_net_reporting_scheduled_export', array( $this, 'process_scheduled_export' ) );

		// maybe disable API logging
		if ( 'on' !== get_option( 'wc_authorize_net_reporting_debug_mode' ) ) {
			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10 );
		}
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 1.8.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Authorize_Net_Reporting\Lifecycle( $this );
	}


	/**
	 * Includes required files.
	 *
	 * @since 1.0
	 */
	private function includes() {

		// load exporter
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-reporting-export.php' );

		if ( is_admin() && ! is_ajax() ) {
			$this->admin_includes();
		}
	}


	/**
	 * Includes required admin files.
	 *
	 * @since 1.0
	 */
	private function admin_includes() {

		// load admin
		$this->admin = $this->load_class( '/includes/admin/class-wc-authorize-net-reporting-admin.php', 'WC_Authorize_Net_Reporting_Admin' );

		// set message handler on admin
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Gets the admin class instance.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_Authorize_Net_Reporting_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the Authorize.Net API object.
	 *
	 * @since 1.0
	 *
	 * @return null|\WC_Authorize_Net_Reporting_API
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$api_login_id        = get_option( 'wc_authorize_net_reporting_api_login_id' );
		$api_transaction_key = get_option( 'wc_authorize_net_reporting_api_transaction_key' );
		$api_environment     = get_option( 'wc_authorize_net_reporting_api_environment' );

		// bail if required info is not available
		if ( ! $api_login_id || ! $api_transaction_key || ! $api_environment ) {
			return null;
		}

		// load API wrapper
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-authorize-net-reporting-api.php' );
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-authorize-net-reporting-api-request.php' );
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-authorize-net-reporting-api-response.php' );

		return $this->api = new \WC_Authorize_Net_Reporting_API( $api_login_id, $api_transaction_key, $api_environment );
	}


	/**
	 * Returns the main Authorize.Net Reporting Instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_Authorize_Net_Reporting
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-authorize-net-reporting/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-authorize-net-reporting/';
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 1.1.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Authorize.Net Reporting', 'woocommerce-authorize-net-reporting' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.1.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page.
	 *
	 * @since 1.1.0
	 *
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-reports&tab=authorize_net&report=1' );
	}


	/**
	 * Checks whether we are on the plugin settings page.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'], $_GET['report'] )
			   && 'wc-reports'    === $_GET['page']
			   && 'authorize_net' === $_GET['tab']
			   && '1'             === (string) $_GET['report'];
	}


	/**
	 * Schedules the once-daily emailed transaction export.
	 *
	 * @since 1.0
	 */
	public function schedule_export() {

		if ( ! wp_next_scheduled( 'wc_authorize_net_reporting_scheduled_export' ) && get_option( 'wc_authorize_net_reporting_email_recipients' ) ) {
			wp_schedule_event( strtotime( 'tomorrow +15 minutes' ), 'daily', 'wc_authorize_net_reporting_scheduled_export' );
		}
	}


	/**
	 * Processes the scheduled export by emailing specified recipients the daily transaction export.
	 *
	 * @since 1.0
	 */
	public function process_scheduled_export() {

		if ( $recipients = get_option( 'wc_authorize_net_reporting_email_recipients' ) ) {

			$export = new \WC_Authorize_Net_Reporting_Export();

			$export->email( $recipients );
		}
	}


}


/**
 * Returns the One True Instance of Authorize.Net Reporting.
 *
 * @since 1.2.0
 *
 * @return \WC_Authorize_Net_Reporting
 */
function wc_authorize_net_reporting() {

	return \WC_Authorize_Net_Reporting::instance();
}
