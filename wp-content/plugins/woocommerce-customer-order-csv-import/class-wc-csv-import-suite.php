<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\CSV_Import_Suite\Background_Fix_Coupons_Usage_Count;

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order/Coupon CSV Import Suite Main Class.
 *
 * @since 1.0.0
 */
class WC_CSV_Import_Suite extends Framework\SV_WC_Plugin {


	/** version number */
	const VERSION = '3.9.0';

	/** @var WC_CSV_Import_Suite single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'csv_import_suite';

	/** @var \WC_CSV_Import_Suite_Admin instance */
	protected $admin;

	/** @var \WC_CSV_Import_Suite_Importers instance */
	protected $importers;

	/** @var \WC_CSV_Import_Suite_Background_Import instance */
	protected $background_import;

	/** @var Background_Fix_Coupons_Usage_Count instance */
	protected $background_fix_coupons_usage_count;

	/** @var \WC_CSV_Import_Suite_AJAX instance */
	protected $ajax;

	/** @var bool $logging_enabled whether debug logging is enabled for the import type */
	private $logging_enabled;


	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain'  => 'woocommerce-csv-import-suite',
				'dependencies' => [
					'php_extensions' => [
						'mbstring',
					],
				],
			]
		);

		// cleanup expired imports
		add_action( 'wc_customer_order_csv_import_scheduled_import_cleanup', [ $this, 'cleanup_imports' ] );
	}


	/**
	 * Loads and initializes the plugin lifecycle handler.
	 *
	 * @since 3.6.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\CSV_Import_Suite\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 3.6.0
	 */
	public function init_plugin() {

		// load required files.
		$this->includes();

		// schedule cleanup of imported files which are older than 14 days
		$this->schedule_import_cleanup();
	}



	/**
	 * Schedule once-daily cleanup of old import jobs.
	 *
	 * @internal
	 *
	 * @since 3.4.0
	 */
	public function schedule_import_cleanup() {

		if ( ! wp_next_scheduled( 'wc_customer_order_csv_import_scheduled_import_cleanup' ) ) {

			wp_schedule_event( strtotime( 'tomorrow +15 minutes' ), 'daily', 'wc_customer_order_csv_import_scheduled_import_cleanup' );
		}
	}


	/**
	 * Includes required files.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function includes() {

		// background job handler to add usage_count meta to coupons that don't have a value defined
		require_once( $this->get_plugin_path() . '/includes/Background_Fix_Coupons_Usage_Count.php' );

		if ( 'yes' !== get_option( 'wc_csv_import_suite_coupons_usage_count_fixed', 'no' ) ) {
			$this->background_fix_coupons_usage_count = new Background_Fix_Coupons_Usage_Count();
		}

		$this->background_import = $this->load_class( '/includes/class-wc-csv-import-suite-background-import.php', 'WC_CSV_Import_Suite_Background_Import' );
		$this->importers         = $this->load_class( '/includes/class-wc-csv-import-suite-importers.php', 'WC_CSV_Import_Suite_Importers' );

		if ( is_admin() ) {
			$this->admin = $this->load_class( '/includes/admin/class-wc-csv-import-suite-admin.php', 'WC_CSV_Import_Suite_Admin' );
		}

		if ( is_ajax() ) {

			require_once( $this->get_plugin_path() . '/includes/class-wc-csv-import-suite-parser.php' );

			$this->ajax = $this->load_class( '/includes/class-wc-csv-import-suite-ajax.php', 'WC_CSV_Import_Suite_AJAX' );
		}
	}


	/**
	 * Gets the admin handler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_CSV_Import_Suite_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the importers handler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_CSV_Import_Suite_Importers
	 */
	public function get_importers_instance() {

		return $this->importers;
	}


	/**
	 * Gets the background import handler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_CSV_Import_Suite_Background_Import
	 */
	public function get_background_import_instance() {

		return $this->background_import;
	}


	/**
	 * Gets the background fix coupons usage count handler instance.
	 *
	 * @since 3.8.3
	 */
	public function get_background_fix_coupons_usage_count_instance() {

		return $this->background_fix_coupons_usage_count;
	}


	/**
	 * Gets the AJAX handler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_CSV_Import_Suite_AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Determines if on the current page is the settings page.
	 *
	 * @since 2.3
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && self::PLUGIN_ID === $_GET['page'];
	}


	/**
	 * Gets the "Import" plugin action link to go directly to the plugin settings page (if any).
	 *
	 * @since 2.3
	 *
	 * @param string|null $plugin_id the plugin identifier
	 * @return string
	 */
	public function get_settings_link( $plugin_id = null ) {

		if ( $settings_url = $this->get_settings_url( $plugin_id ) ) {
			$settings_url = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Import', 'woocommerce-csv-import-suite' ) );
		} else {
			$settings_url = '';
		}

		return $settings_url;
	}


	/**
	 * Gets the plugin configuration URL.
	 *
	 * @since 2.3
	 *
	 * @param string|null $plugin_id the plugin identifier
	 * @return string
	 */
	public function get_settings_url( $plugin_id = null ) {

		// link to the import page
		return admin_url( 'admin.php?page=' . self::PLUGIN_ID );
	}


	/**
	 * Gets the plugin documentation url.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/customer-order-csv-import-suite/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 3.6.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/customerorder-csv-import-suite/';
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 2.3
	 *
	 * @return string
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Customer/Order/Coupon CSV Import', 'woocommerce-csv-import-suite' );
	}


	/**
	 * Gets __FILE__.
	 *
	 * @since 2.3
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the main Customer/Order/Coupon CSV Import Suite instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 2.7.0
	 *
	 * @return WC_CSV_Import_Suite
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Adds an entry to the debug log if enabled.
	 *
	 * @since 3.1.1
	 *
	 * @param string $message the log message
	 * @param null $_ unused
	 */
	public function log( $message, $_ = null ) {

		if ( $this->logging_enabled() ) {
			parent::log( $message );
		}
	}


	/**
	 * Determines if debug logging is enabled for a given importer.
	 *
	 * @since 3.1.1
	 *
	 * @return bool
	 */
	public function logging_enabled() {

		$this->logging_enabled = 'yes' === get_option( 'wc_csv_import_suite_debug_mode', 'no' );

		/**
		 * Filters whether debug logging is enabled.
		 *
		 * @since 3.1.1
		 *
		 * @param bool $logging_enabled whether logging is enabled
		 */
		return (bool) apply_filters( 'wc_csv_import_suite_logging_enabled', $this->logging_enabled );
	}


	/**
	 * Cleans up (removes) imported files which are older than 14 days.
	 *
	 * @internal
	 *
	 * @since 3.4.0
	 */
	public function cleanup_imports() {

		wc_csv_import_suite()->get_background_import_instance()->remove_expired_imports();
	}


	/**
	 * Removes the import finished notice from user meta.
	 *
	 * @since 3.1.0
	 *
	 * @param string $import_id Import job ID
	 * @param int $user_id
	 */
	public function remove_import_finished_notice( $import_id, $user_id ) {

		$import_notices = get_user_meta( $user_id, '_wc_csv_import_suite_notices', true );

		if ( ! empty( $import_notices ) && in_array( $import_id, $import_notices, true ) ) {

			unset( $import_notices[ array_search( $import_id, $import_notices, false ) ] );

			update_user_meta( $user_id, '_wc_csv_import_suite_notices', $import_notices );
		}

		// also remove the message from user dismissed notices
		$dismissed_notices = wc_csv_import_suite()->get_admin_notice_handler()->get_dismissed_notices( $user_id );
		$message_id        = 'wc_csv_import_suite_finished_' . $import_id;

		if ( ! empty( $dismissed_notices ) && isset( $dismissed_notices[ $message_id ] ) ) {

			unset( $dismissed_notices[ $message_id ] );

			update_user_meta( $user_id, '_wc_plugin_framework_csv_import_suite_dismissed_messages', $dismissed_notices );
		}
	}


}


/**
 * Returns the One True Instance of Customer/Order/Coupon CSV Import Suite.
 *
 * @since 2.7.0
 *
 * @return \WC_CSV_Import_Suite
 */
function wc_csv_import_suite() {

	return WC_CSV_Import_Suite::instance();
}
