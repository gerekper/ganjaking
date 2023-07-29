<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Automations\Scheduler;
use SkyVerge\WooCommerce\CSV_Export\Background_Mark_Exported;
use SkyVerge\WooCommerce\CSV_Export\Integrations\Integrations;
use SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * WooCommerce Customer/Order/Coupon Export plugin class.
 *
 * @since 1.0.0
 */
class WC_Customer_Order_CSV_Export extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '5.5.0';

	/** @var WC_Customer_Order_CSV_Export single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'customer_order_export';

	/** export types */
	const EXPORT_TYPE_ORDERS    = 'orders';
	const EXPORT_TYPE_CUSTOMERS = 'customers';
	const EXPORT_TYPE_COUPONS   = 'coupons';

	/** output types */
	const OUTPUT_TYPE_CSV = 'csv';
	const OUTPUT_TYPE_XML = 'xml';

	/** @var \WC_Customer_Order_CSV_Export_Admin instance */
	protected $admin;

	/** @var \WC_Customer_Order_CSV_Export_Compatibility instance */
	protected $compatibility;

	/** @var \WC_Customer_Order_CSV_Export_Formats instance */
	protected $formats;

	/** @var \WC_Customer_Order_CSV_Export_Methods instance */
	protected $methods;

	/** @var Scheduler instance */
	protected $automation_scheduler;

	/** @var \WC_Customer_Order_CSV_Export_AJAX instance */
	protected $ajax;

	/** @var \WC_Customer_Order_CSV_Export_Background_Export instance */
	protected $background_export;

	/** @var Background_Mark_Exported instance */
	private $background_mark_exported;

	/** @var \WC_Customer_Order_CSV_Export_Batch_Export_Handler instance */
	protected $batch_export;

	/** @var \WC_Customer_Order_CSV_Export_Download_Handler instance */
	protected $download_handler;

	/** @var \WC_Customer_Order_CSV_Export_Handler instance */
	protected $export_handler;

	/** @var \SkyVerge\WooCommerce\CSV_Export\Hook_Deprecator hook deprecator instance */
	protected $hook_deprecator_instance;

	/** @var \SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler instance */
	protected $taxonomies_handler;

	/** @var \SkyVerge\WooCommerce\CSV_Export\Integrations\Integrations integrations instance */
	protected $integrations;


	/**
	 * Setup main plugin class
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain'        => 'woocommerce-customer-order-csv-export',
				'supports_hpos'      => true,
				'dependencies'       => [
					'php_extensions' => $this->get_extension_dependencies(),
					'php_functions'  => $this->get_function_dependencies(),
				],
			]
		);

		add_action( 'admin_action_wc_customer_order_export_migrate_from_xml', function() {

			if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( Framework\SV_WC_Helper::get_requested_value( 'nonce' ), 'wc_customer_order_export_migrate_from_xml' ) ) {
				wp_die();
			}

			$this->get_lifecycle_handler()->migrate_from_xml();

			wp_safe_redirect( wp_get_referer() );
			exit;

		} );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 */
	public function init_plugin() {

		// required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			if ( ! wp_doing_ajax() ) {
				$this->admin_includes();
			} else {
				$this->ajax_includes();
			}
		}

		// clear scheduled events on deactivation
		register_deactivation_hook( $this->get_file(), Scheduler::class . '::clear_scheduled_actions' );
	}


	/**
	 * Loads and initializes the plugin lifecycle handler.
	 *
	 * @since 4.7.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/class-wc-customer-order-csv-export-lifecycle.php' );

		$this->lifecycle_handler = new WC_Customer_Order_CSV_Export_Lifecycle( $this );
	}


	/**
	 * Initializes the hook deprecator.
	 *
	 * @since 5.0.0
	 */
	protected function init_hook_deprecator() {

		require_once( $this->get_plugin_path() . '/src/Hook_Deprecator.php' );

		$this->hook_deprecator_instance = new \SkyVerge\WooCommerce\CSV_Export\Hook_Deprecator( $this );
	}


	/**
	 * Includes required classes
	 *
	 * @since 3.0.0
	 */
	public function includes() {

		// Background export must be loaded all the time, because
		// otherwise background jobs simply won't work
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-async-request.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-background-job-handler.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-job-batch-handler.php' );

		// automations
		require_once( $this->get_plugin_path() . '/src/Automations/Automation.php' );
		require_once( $this->get_plugin_path() . '/src/Automations/Automation_Data_Store_Options.php' );
		require_once( $this->get_plugin_path() . '/src/Automations/Automation_Factory.php' );

		// export class
		require_once( $this->get_plugin_path() . '/src/class-wc-customer-order-csv-export-export.php' );

		// handles data storage
		require_once( $this->get_plugin_path() . '/src/data-stores/abstract-class-wc-customer-order-csv-export-data-store.php' );
		require_once( $this->get_plugin_path() . '/src/data-stores/class-wc-customer-order-csv-export-data-store-factory.php' );

		// export functions
		require_once( $this->get_plugin_path() . '/src/functions/wc-customer-order-csv-export-export-functions.php' );

		// handles exporting files in background
		$this->background_export = $this->load_class( '/src/class-wc-customer-order-csv-export-background-export.php', 'WC_Customer_Order_CSV_Export_Background_Export' );

		// handles marking orders as exported in background
		require_once( $this->get_plugin_path() . '/src/Background_Mark_Exported.php' );
		$this->background_mark_exported = new Background_Mark_Exported();

		require_once( $this->get_plugin_path() . '/src/class-wc-customer-order-csv-export-batch-export-handler.php' );

		// handles exporting files in batches
		$this->batch_export = new WC_Customer_Order_CSV_Export_Batch_Export_Handler( $this->background_export, $this );

		// general interface for interacting with exports
		$this->export_handler = $this->load_class( '/src/class-wc-customer-order-csv-export-handler.php', 'WC_Customer_Order_CSV_Export_Handler' );

		// compatibility for legacy export formats and other extensions
		$this->compatibility = $this->load_class( '/src/class-wc-customer-order-csv-export-compatibility.php', 'WC_Customer_Order_CSV_Export_Compatibility' );

		// formats definitions
		$this->formats = $this->load_class( '/src/class-wc-customer-order-csv-export-formats.php', 'WC_Customer_Order_CSV_Export_Formats' );

		// export methods
		$this->methods = $this->load_class( '/src/class-wc-customer-order-csv-export-methods.php', 'WC_Customer_Order_CSV_Export_Methods' );

		// handles exported file downloads
		$this->download_handler = $this->load_class( '/src/class-wc-customer-order-csv-export-download-handler.php', 'WC_Customer_Order_CSV_Export_Download_Handler' );

		// handles scheduling and execution of automatic export / upload
		$this->automation_scheduler = $this->load_class( '/src/Automations/Scheduler.php', Scheduler::class );

		// handles custom taxonomy
		$this->taxonomies_handler = $this->load_class( '/src/Taxonomies_Handler.php', Taxonomies_Handler::class );

		// handles integrations
		$this->integrations = $this->load_class( '/src/Integrations/Integrations.php', Integrations::class );

		require_once( $this->get_plugin_path() . '/src/Export_Formats/Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/CSV/CSV_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/CSV/Orders_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/CSV/Customers_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/CSV/Coupons_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/XML/XML_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/XML/Orders_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/XML/Customers_Export_Format_Definition.php' );
		require_once( $this->get_plugin_path() . '/src/Export_Formats/XML/Coupons_Export_Format_Definition.php' );

		require_once( $this->get_plugin_path() . '/src/export-methods/interface-wc-customer-order-csv-export-method.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/ftp/abstract-wc-customer-order-csv-export-method-file-transfer.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/ftp/class-wc-customer-order-csv-export-method-sftp.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/ftp/class-wc-customer-order-csv-export-method-ftp-implicit-ssl.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/ftp/class-wc-customer-order-csv-export-method-ftp.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/class-wc-customer-order-csv-export-method-http-post.php' );
		require_once( $this->get_plugin_path() . '/src/export-methods/class-wc-customer-order-csv-export-method-email.php' );

		require_once( $this->get_plugin_path() . '/src/admin/Admin_Custom_Formats.php' );
		require_once( $this->get_plugin_path() . '/src/admin/Export_Formats_Helper.php' );
	}


	/**
	 * Loads the Admin classes
	 *
	 * @since 3.0.0
	 */
	public function admin_includes() {

		require_once( $this->get_plugin_path() . '/src/admin/Automations.php' );
		require_once( $this->get_plugin_path() . '/src/admin/Meta_Boxes/Exported_By.php' );

		// loads the admin settings page and adds functionality to the order admin
		$this->admin = $this->load_class( '/src/admin/class-wc-customer-order-csv-export-admin.php', 'WC_Customer_Order_CSV_Export_Admin' );

		// message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Loads the AJAX classes
	 *
	 * @since 4.0.0
	 */
	public function ajax_includes() {

		$this->ajax = $this->load_class( '/src/class-wc-customer-order-csv-export-ajax.php', 'WC_Customer_Order_CSV_Export_AJAX' );
	}


	/**
	 * Return admin class instance
	 *
	 * @since 3.12.0
	 * @return \WC_Customer_Order_CSV_Export_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Return compatibility class instance
	 *
	 * @since 3.12.0
	 * @return \WC_Customer_Order_CSV_Export_Compatibility
	 */
	public function get_compatibility_instance() {
		return $this->compatibility;
	}


	/**
	 * Return formats class instance
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Formats
	 */
	public function get_formats_instance() {
		return $this->formats;
	}


	/**
	 * Return methods class instance
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Methods
	 */
	public function get_methods_instance() {
		return $this->methods;
	}


	/**
	 * Returns cron class instance.
	 *
	 * @since 3.12.0
	 * @deprecated 5.0.0
	 */
	public function get_cron_instance() {

		_deprecated_function( __METHOD__, '5.0.0' );
	}


	/**
	 * Returns the Automation Scheduler class instance.
	 *
	 * @since 5.0.0
	 *
	 * @return Scheduler
	 */
	public function get_automation_scheduler_instance() {
		return $this->automation_scheduler;
	}


	/**
	 * Return ajax class instance
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Return background export class instance
	 *
	 * @since 4.0.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Background_Export
	 */
	public function get_background_export_instance() {
		return $this->background_export;
	}


	/**
	 * Gets the background mark exported class instance.
	 *
	 * @since 5.0.8
	 *
	 * @return Background_Mark_Exported
	 */
	public function get_background_mark_exported_instance() {
		return $this->background_mark_exported;
	}


	/**
	 * Return batch export class instance
	 *
	 * @since 4.0.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Batch_Export_Handler
	 */
	public function get_batch_export_instance() {
		return $this->batch_export;
	}


	/**
	 * Return download handler class instance
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Download_Handler
	 */
	public function get_download_handler_instance() {
		return $this->download_handler;
	}


	/**
	 * Return export handler class instance
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Handler
	 */
	public function get_export_handler_instance() {
		return $this->export_handler;
	}


	/** Admin Methods ******************************************************/


	/**
	 * Render a notice for the user to select their desired export format
	 *
	 * @since 3.4.0
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {
		global $wpdb;

		// show any dependency notices
		parent::add_admin_notices();

		if ( get_option( 'wc_customer_order_export_upgraded' ) ) {

			// add an admin notice
			wc_customer_order_csv_export()->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s - opening <a> link tag, %3$s closing </a> link tag, %4$s - opening <a> link tag, %5$s closing </a> link tag, %6$s - opening <a> link tag, %7$s closing </a> link tag */
					__( 'The %1$s plugin can now export to XML! To start using XML exports, please setup your first %2$sautomated%3$s or %4$smanual%5$s export, or read the %6$supdated documentation%7$s to learn more.', 'woocommerce-customer-order-csv-export' ),
					$this->get_plugin_name(),
					'<a href="' . $this->get_settings_url( null, WC_Customer_Order_CSV_Export_Admin::TAB_AUTOMATIONS ) . '">',
					'</a>',
					'<a href="' . $this->get_settings_url() . '">',
					'</a>',
					'<a href="' . $this->get_documentation_url() . '">',
					'</a>'
				),
				'wc-customer-order-csv-export-upgraded-xml',
				[
					'always_show_on_settings' => false,
					'notice_class'            => 'updated',
				]
			);
		}

		if ( $this->is_plugin_settings() ) {

			$loopback_enabled = $this->get_background_export_instance()->test_connection();

			// add notice for failing loopback connections
			if ( ! $loopback_enabled ) {

				$message = sprintf(
					/* translators: Placeholders: %1$s - <strong>; %2$s - </strong>; %3$s, %5$s - <a> tags; %4$s - </a> tag */
					__( '%1$sAutomated Exports%2$s are currently unavailable because your site does not support background processing. To use automated exports, please ask your hosting company to ensure your server has %3$sloopback connections%4$s enabled, or switch to a %5$srecommended hosting provider%4$s.', 'woocommerce-customer-order-csv-export' ),
					'<strong>',
					'</strong>',
					'<a href="https://docs.woocommerce.com/document/ordercustomer-csv-export/#faq-loopback" target="_blank">',
					'</a>',
					'<a href="https://www.skyverge.com/upgrading-php-versions/#recommended-hosts" target="_blank">'
				);

				$message .= ' ' . sprintf(
					/* translators: Placeholders: %1$s - <strong>; %2$s - </strong> */
					__( 'In the meantime, you can process manual exports by enabling the %1$sBatch processing%2$s setting.', 'woocommerce-customer-order-csv-export' ),
					'<strong>', '</strong>'
				);

				$this->get_admin_notice_handler()->add_admin_notice(
					$message,
					'export-loopback-notice',
					[ 'notice_class' => 'error' ]
				);
			}
		}

		// add notice for mysqli requirement
		if ( ( $this->is_export_page() || $this->is_export_list_page() ) && ! $wpdb->dbh instanceof mysqli ) {

			$message = sprintf(
			/* translators: Placeholders: %1$s - <a> tag; %2$s - </a> tag */
				__( 'Heads up! Your exports may consume more memory and take longer than usual unless mysqli is installed and enabled on your site. %1$sLearn More%2$s', 'woocommerce-customer-order-csv-export' ),
				'<a href="https://docs.woocommerce.com/document/ordercustomer-csv-export/#mysqli-streaming" target="_blank">',
				'</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice(
				$message,
				'mysqli-not-found-notice',
				[ 'dismissible' => false, 'notice_class' => 'error' ]
			);
		}

		// add notice after successful migration from XML Export Suite
		if ( get_option( 'wc_customer_order_export_migrated_from_xml_export_suite' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - opening <a> link tag, %2$s closing </a> link tag */
					__( 'Your settings and data have been migrated from XML Export Suite. %1$sClick here%2$s to view your automated exports and custom formats.', 'woocommerce-customer-order-csv-export' ),
					'<a href="' . esc_url( $this->get_settings_url() ) . '">',
					'</a>'
				),
				'wc-customer-order-export-migrated-from-xml',
				[
					'always_show_on_settings' => false,
					'notice_class'            => 'updated',
				]
			);

		} elseif ( get_option( 'wc_customer_order_export_offer_xml_migration' ) ) {

			// add admin notice prompting to migrate from XML Export Suite
			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s - opening <a> link tag, %3$s closing </a> link tag */
					__( 'The %1$s plugin can now export to XML! Please %2$sclick here to migrate your settings and data from XML Export Suite%3$s.', 'woocommerce-customer-order-csv-export' ),
					$this->get_plugin_name(),
					'<a href="' . esc_url( add_query_arg( [
						'action' => 'wc_customer_order_export_migrate_from_xml',
						'nonce'  => wp_create_nonce( 'wc_customer_order_export_migrate_from_xml' ),
					], 'admin.php' ) ) . '">', '</a>'
				),
				'wc-customer-order-export-migrate-from-xml',
				[
					'always_show_on_settings' => false,
					'notice_class'            => 'updated',
				]
			);

		} else {

			// add notice for selecting export format
			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s - opening <a> link tag, %3$s - closing </a> link tag, %4$s - opening <a> link tag, %5$s - closing </a> link tag */
					__( 'Thanks for installing the %1$s plugin! To get started, please setup your first %2$sautomated%3$s or %4$smanual%5$s export. ', 'woocommerce-customer-order-csv-export' ),
					$this->get_plugin_name(),
					'<a href="' . $this->get_settings_url( null, WC_Customer_Order_CSV_Export_Admin::TAB_AUTOMATIONS ) . '">',
					'</a>',
					'<a href="' . $this->get_settings_url() . '">',
					'</a>'
				),
				'export-format-notice',
				[
					'always_show_on_settings' => false,
					'notice_class'            => 'updated'
				]
			);
		}

		$migrated_free_addons = get_option( 'wc_customer_order_export_free_add_ons_migrated' );

		if ( ! empty( $migrated_free_addons ) && is_array( $migrated_free_addons ) ) {

			$migrated_add_on_notice_args = [
				'dismissible'             => true,
				'always_show_on_settings' => false,
				'notice_class'            => 'notice-info'
			];

			if ( in_array( 'woocommerce-order-export-refunds-only', $migrated_free_addons, true ) ) {
				$this->get_admin_notice_handler()->add_admin_notice(
					sprintf(
						/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing <strong> HTML tag */
						__( '%1$sHeads up!%2$s We\'ve merged the Export Refunds Only add-on into Customer/Order/Coupon Export, so you no longer need this add-on to export only refunded orders. This add-on has been deactivated and can be safely removed from your plugin list.', 'woocommerce-customer-order-csv-export' ),
						'<strong>', '</strong>'
					),
					'woocommerce-order-export-refunds-only-migrated',
					$migrated_add_on_notice_args
				);
			}

			if ( in_array( 'woocommerce-order-export-vat', $migrated_free_addons, true ) ) {
				$this->get_admin_notice_handler()->add_admin_notice(
					sprintf(
						/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing <strong> HTML tag */
						__( '%1$sHeads up!%2$s We\'ve merged the Export VAT Number add-on into Customer/Order/Coupon Export, so you no longer need this add-on to include VAT numbers on order exports. This add-on has been deactivated and can be safely removed from your plugin list.', 'woocommerce-customer-order-csv-export' ),
						'<strong>', '</strong>'
					),
					'woocommerce-order-export-vat-migrated',
					$migrated_add_on_notice_args
				);
			}
		}
	}


	/** Helper Methods ******************************************************/


	/**
	 * Main Customer/Order CSV Export Instance, ensures only one instance is/can be loaded
	 *
	 * @since 3.9.0
	 * @see wc_customer_order_csv_export()
	 * @return WC_Customer_Order_CSV_Export
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.0.0
	 * @see Framework\SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Customer/Order/Coupon Export', 'woocommerce-customer-order-csv-export' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.0.0
	 * @see Framework\SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation url, which for Customer/Order CSV Export is non-standard
	 *
	 * @since 3.0.0
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/ordercustomer-csv-export/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 3.10.0
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the URL to the settings page.
	 *
	 * In 5.0.0 replaced $output_type param with $tab
	 *
	 * @since 3.0.0
	 *
	 * @see Framework\SV_WC_Plugin::is_plugin_settings()
	 * @param null|string $plugin_id Unused
	 * @param string $tab settings tab, either `export`, `export_list`, `automations`, or `custom_formats`
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $plugin_id = null, $tab = 'export' ) {

		return admin_url( 'admin.php?page=wc_customer_order_csv_export&tab=' . $tab );
	}


	/**
	 * Determines if the current page is the plugin settings page.*
	 *
	 * @since 4.4.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return is_admin() && 'wc_customer_order_csv_export' === Framework\SV_WC_Helper::get_requested_value( 'page' );
	}


	/**
	 * Determines if the current page is the export list page.
	 *
	 * @since 4.5.0
	 *
	 * @return bool
	 */
	public function is_export_page() {

		return is_admin() && 'wc_customer_order_csv_export' === Framework\SV_WC_Helper::get_requested_value( 'page' ) && 'export_list' === Framework\SV_WC_Helper::get_requested_value( 'tab' );
	}


	/**
	 * Determines if the current page is the new export page.
	 *
	 * @since 4.5.0
	 *
	 * @return bool
	 */
	public function is_export_list_page() {

		return is_admin() && 'wc_customer_order_csv_export' === Framework\SV_WC_Helper::get_requested_value( 'page' ) && 'export' === Framework\SV_WC_Helper::get_requested_value( 'tab' );
	}


	/**
	 * Gets conditional PHP extension dependencies based on the FTP security selected.
	 *
	 * @since 5.2.0
	 *
	 * @return array of required PHP extensions
	 */
	protected function get_extension_dependencies() {

		$dependencies = [ 'mbstring' ];

		// check if FTP is one of the chosen export methods
		if ( ! in_array( 'ftp', $this->get_auto_export_methods(), true ) ) {
			return $dependencies;
		}

		$ftp_securities = $this->get_auto_export_ftp_securities();

		if ( in_array( 'sftp', $ftp_securities, true ) ) {

			$dependencies[] = 'ssh2';
		}

		if ( in_array( 'ftp_ssl', $ftp_securities, true ) ) {

			$dependencies[] = 'curl';
		}

		if ( in_array( 'ftps', $ftp_securities, true ) ) {

			$dependencies[] = 'ftp';
			$dependencies[] = 'openssl';
		}

		return $dependencies;
	}


	/**
	 * Gets conditional function dependencies based on the FTP security selected.
	 *
	 * @since 3.1.0
	 *
	 * @return array of required php functions
	 */
	protected function get_function_dependencies() {

		// check if FTP is one of the chosen export methods
		if ( ! in_array( 'ftp', $this->get_auto_export_methods(), true ) ) {
			return [];
		}

		$ftp_securities = $this->get_auto_export_ftp_securities();

		if ( in_array( 'ftps', $ftp_securities, true ) ) {

			return [ 'ftp_ssl_connect' ];
		}

		return [];
	}


	/**
	 * Get auto export methods used by export types
	 *
	 * @since 4.0.0
	 * @return array
	 */
	private function get_auto_export_methods() {

		$export_types   = [ self::EXPORT_TYPE_CUSTOMERS, self::EXPORT_TYPE_ORDERS ];
		$output_types   = [ self::OUTPUT_TYPE_CSV, self::OUTPUT_TYPE_XML ];
		$export_methods = [];

		foreach ( $export_types as $export_type ) {
			foreach ( $output_types as $output_type ) {
				$export_methods[] = get_option( 'wc_customer_order_export_' . $output_type . '_' . $export_type . '_auto_export_method' );
			}
		}

		return $export_methods;
	}


	/**
	 * Get auto export methods used by export types
	 *
	 * @since 4.0.0
	 * @return array
	 */
	private function get_auto_export_ftp_securities() {

		$export_types = [ self::EXPORT_TYPE_CUSTOMERS, self::EXPORT_TYPE_ORDERS ];
		$output_types = [ self::OUTPUT_TYPE_CSV, self::OUTPUT_TYPE_XML ];
		$securities   = [];

		foreach ( $export_types as $export_type ) {
			foreach ( $output_types as $output_type ) {
				$securities[] = get_option( 'wc_customer_order_export_' . $output_type . '_' . $export_type . '_ftp_security' );
			}
		}

		return $securities;
	}


	/**
	 * Determines if batch processing is enabled.
	 *
	 * @since 4.4.0
	 * @deprecated 5.0.0
	 *
	 * @return bool
	 */
	public function is_batch_processing_enabled() {

		wc_deprecated_function( __METHOD__, '5.0.0' );

		// account for changes while saving settings
		if ( isset( $_POST['wc_customer_order_export_enable_batch_processing'] ) ) {
			return (bool) $_POST['wc_customer_order_export_enable_batch_processing'];
		}

		return 'yes' === get_option( 'wc_customer_order_export_enable_batch_processing', 'no' );
	}


	/**
	 * Gets the known export types.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_export_types() {

		$types = [
			self::EXPORT_TYPE_ORDERS    => __( 'Orders', 'woocommerce-customer-order-csv-export' ),
			self::EXPORT_TYPE_CUSTOMERS => __( 'Customers', 'woocommerce-customer-order-csv-export' ),
			self::EXPORT_TYPE_COUPONS   => __( 'Coupons', 'woocommerce-customer-order-csv-export' ),
		];

		return (array) apply_filters( 'wc_customer_order_export_export_types', $types );
	}


	/**
	 * Gets the known output types, like CSV and XML.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_output_types() {

		$types = [
			self::OUTPUT_TYPE_CSV => _x( 'CSV', 'export output type', 'woocommerce-customer-order-csv-export' ),
			self::OUTPUT_TYPE_XML => _x( 'XML', 'export output type', 'woocommerce-customer-order-csv-export' ),
		];

		/**
		 * Filters the known output types, like CSV and XML.
		 *
		 * @since 5.0.0
		 *
		 * @param array
		 */
		return (array) apply_filters( 'wc_customer_order_export_output_types', $types );
	}


	/** Deprecated methods ********************************************************************************************/


	/**
	 * Map a deprecated/renamed filter to a new one.
	 *
	 * @since 4.0.0
	 * @deprecated 5.0.0
	 */
	public function map_deprecated_filter() {

		wc_deprecated_function( __METHOD__, '5.0.0' );
	}


}


/**
 * Returns the One True Instance of Customer/Order CSV Export
 *
 * @since 3.9.0
 * @return WC_Customer_Order_CSV_Export instance of Customer/Order CSV Export main class
 */
function wc_customer_order_csv_export() {
	return WC_Customer_Order_CSV_Export::instance();
}
