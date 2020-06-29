<?php
/**
 * Plugin Name: WooCommerce Customer/Order XML Export Suite
 * Plugin URI: http://www.woocommerce.com/products/customerorder-xml-export-suite/
 * Description: Easily download customers & orders in XML format and automatically export FTP or HTTP POST on a recurring schedule
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 2.6.3
 * Text Domain: woocommerce-customer-order-xml-export-suite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2019, SkyVerge (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Customer-Order-XML-Export-Suite
 * @author      SkyVerge
 * @category    Export
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 187889:5c165d4e132d8cf5a6d6555daf358041
 * WC requires at least: 3.0.9
 * WC tested up to: 3.8.1
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '5c165d4e132d8cf5a6d6555daf358041', '187889' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce Customer/Order XML Export Suite', 'woocommerce-customer-order-xml-export-suite' ), __FILE__, 'init_woocommerce_customer_order_xml_export_suite', array(
	'minimum_wc_version'   => '3.0.9',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_customer_order_xml_export_suite() {

/**
 * The main class for the Customer/Order XML export.
 *
 * @since 1.0.0
 */
class WC_Customer_Order_XML_Export_Suite extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.6.3';

	/** @var WC_Customer_Order_XML_Export_Suite single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'customer_order_xml_export_suite';

	/** @var \WC_Customer_Order_XML_Export_Suite_Admin instance */
	protected $admin;

	/** @var \WC_Customer_Order_XML_Export_Suite_Formats instance */
	protected $formats;

	/** @var \WC_Customer_Order_XML_Export_Suite_Methods instance */
	protected $methods;

	/** @var \WC_Customer_Order_XML_Export_Suite_Cron instance */
	protected $cron;

	/** @var \WC_Customer_Order_XML_Export_Suite_AJAX instance */
	protected $ajax;

	/** @var \WC_Customer_Order_XML_Export_Suite_Background_Export instance */
	protected $background_export;

	/** @var \WC_Customer_Order_XML_Export_Suite_Batch_Export_Handler instance */
	protected $batch_export;

	/** @var \WC_Customer_Order_XML_Export_Suite_Download_Handler instance */
	protected $download_handler;

	/** @var \WC_Customer_Order_XML_Export_Suite_Handler instance */
	protected $export_handler;

	/** @var array deprectaed filter mapping, old => new **/
	protected $deprecated_filters = array(
		'wc_customer_order_xml_export_suite_export_file_name'               => 'wc_customer_order_xml_export_suite_filename',
		'wc_customer_order_xml_export_suite_admin_query_args'               => 'wc_customer_order_xml_export_suite_query_args',
		'wc_customer_order_xml_export_suite_admin_user_query_args'          => 'wc_customer_order_xml_export_suite_user_query_args',
		'wc_customer_order_xml_export_suite_order_export_format'            => 'wc_customer_order_xml_export_suite_orders_xml_data',
		'wc_customer_order_xml_export_suite_order_export_order_list_format' => 'wc_customer_order_xml_export_suite_order_data',
		'wc_customer_order_xml_export_suite_order_export_line_item_format'  => 'wc_customer_order_xml_export_suite_order_line_item',
		'wc_customer_order_xml_export_suite_order_export_order_note_format' => 'wc_customer_order_xml_export_suite_order_note',
		'wc_customer_order_xml_export_suite_customer_export_data'           => 'wc_customer_order_xml_export_suite_customer_data',
		'wc_customer_order_xml_export_suite_customer_export_format'         => 'wc_customer_order_xml_export_suite_customers_xml_data',
	);


	/**
	 * Setup main plugin class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-customer-order-xml-export-suite',
				'display_php_notice' => true,
				'dependencies'       => array(
					'extensions'         => array(
						'mbstring'
					)
				)
			)
		);

		// required files
		$this->includes();

		// Set orders as not-exported when created
		add_action( 'wp_insert_post',  array( $this, 'mark_order_not_exported' ), 10, 2 );

		// Set users as not-exported when created
		add_action( 'user_register',  array( $this, 'mark_user_not_exported' ), 1 );

		// Admin
		if ( is_admin() ) {

			if ( ! is_ajax() ) {

				$this->admin_includes();

				add_action( 'admin_action_wc_customer_order_xml_export_suite_install_unified_plugin', array( $this, 'install_unified_plugin' ) );

			} else {
				$this->ajax_includes();
			}
		}

		// Handle renamed filters
		foreach ( $this->deprecated_filters as $new_filter ) {

			// we need to pass all the args to the filter, but there's no way to tell apply_filters()
			// to pass them all to the function (why, WP, why?), so we'll need to use an arbitrary
			// value which is great enough so that it covers all our arguments
			add_filter( $new_filter, array( $this, 'map_deprecated_filter' ), 10, 10 );
		}

		// clear scheduled events on deactivation
		register_deactivation_hook( $this->get_file(), array( $this->get_cron_instance(), 'clear_scheduled_export' ) );
	}


	/**
	 * Map a deprecated/renamed filter to a new one
	 *
	 * This method works by hooking into the new, renamed version of the filter
	 * and checking if any filters are hooked into the old hook. It then runs
	 * these filters and applies the data modifications in the new filter, and
	 * finally deprecates the filter using `_deprecated_function`.
	 *
	 * It assumes that the filter arguments match. If the args do not match,
	 * consider deprecating using SV_WC_Hook_Deprecator instead.
	 *
	 * @since 2.0.0
	 * @return mixed
	 */
	public function map_deprecated_filter() {

		$args   = func_get_args();
		$data   = $args[0];
		$filter = current_filter();

		// check if there is a matching old filter for the current filter
		if ( $old_filter = array_search( $filter, $this->deprecated_filters ) ) {

			// check if there are any filters added to the old filter
			if ( has_filter( $old_filter ) ) {

				// prepend old filter name to the args
				array_unshift( $args, $old_filter );

				// apply the filters attached to the old filter hook to $data
				$data = call_user_func_array( 'apply_filters', $args );

				_deprecated_function( 'The ' . $old_filter . ' filter', '2.0.0', $filter );
			}
		}

		return $data;
	}


	/**
	 * Set each new order as not exported. This is done because querying orders that have a specific meta key / value
	 * is much more reliable than querying orders that don't have a specific meta key / value AND prevents accidental
	 * export of a massive set of old orders on first run
	 *
	 * @since 1.0.0
	 * @param int $post_id new order ID
	 * @param object $post the post object
	 */
	public function mark_order_not_exported( $post_id, $post ) {

		if ( $post->post_type == 'shop_order' ) {

			// force unique, because oddly this can be invoked when changing the status of an existing order
			add_post_meta( $post_id, '_wc_customer_order_xml_export_suite_is_exported', 0, true );
			add_post_meta( $post_id, '_wc_customer_order_xml_export_suite_customer_is_exported', 0, true );
		}
	}


	/**
	 * Set each new user as not exported. This is done because querying users that have a specific meta key / value
	 * is much more reliable than querying users that don't have a specific meta key / value AND prevents accidental
	 * export of a massive set of old customers on first run
	 *
	 * @since 2.0.0
	 * @param int $user_id new user ID
	 * @param object $post the post object
	 */
	public function mark_user_not_exported( $user_id ) {

		add_user_meta( $user_id, '_wc_customer_order_xml_export_suite_is_exported', 0, true );
	}


	/**
	 * Includes required classes
	 *
	 * @since 1.1.0
	 */
	public function includes() {

		// Background export must be loaded all the time, because
		// otherwise background jobs simply won't work
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-async-request.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-background-job-handler.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-job-batch-handler.php' );

		// export class
		require_once( $this->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-export.php' );

		// handles data storage
		require_once( $this->get_plugin_path() . '/includes/data-stores/abstract-class-wc-customer-order-xml-export-suite-data-store.php' );
		require_once( $this->get_plugin_path() . '/includes/data-stores/class-wc-customer-order-xml-export-suite-data-store-factory.php' );

		// export functions
		require_once( $this->get_plugin_path() . '/includes/functions/wc-customer-order-xml-export-suite-export-functions.php' );

		// handles exporting files in background
		$this->background_export = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-background-export.php', 'WC_Customer_Order_XML_Export_Suite_Background_Export' );

		require_once( $this->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-batch-export-handler.php' );

		// handles exporting files in batches
		$this->batch_export = new WC_Customer_Order_XML_Export_Suite_Batch_Export_Handler( $this->background_export, $this );

		// general interface for interacting with exports
		$this->export_handler = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-handler.php', 'WC_Customer_Order_XML_Export_Suite_Handler' );

		// formats definitions
		$this->formats = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-formats.php', 'WC_Customer_Order_XML_Export_Suite_Formats' );

		// export methods
		$this->methods = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-methods.php', 'WC_Customer_Order_XML_Export_Suite_Methods' );

		// handles exported file downloads
		$this->download_handler = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-download-handler.php', 'WC_Customer_Order_XML_Export_Suite_Download_Handler' );

		// handles scheduling and execution of automatic export / upload
		$this->cron = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-cron.php', 'WC_Customer_Order_XML_Export_Suite_Cron' );

		require_once( $this->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-unified-installer.php' );
	}


	/**
	 * Loads the Admin & AJAX classes
	 *
	 * @since 1.1.0
	 */
	public function admin_includes() {

		// loads the admin settings page and adds functionality to the order admin
		$this->admin = $this->load_class( '/includes/admin/class-wc-customer-order-xml-export-suite-admin.php', 'WC_Customer_Order_XML_Export_Suite_Admin' );

		// add message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Loads the AJAX classes
	 *
	 * @since 2.0.0
	 */
	public function ajax_includes() {

		$this->ajax = $this->load_class( '/includes/class-wc-customer-order-xml-export-suite-ajax.php', 'WC_Customer_Order_XML_Export_Suite_AJAX' );
	}


	/**
	 * Return deprecated/removed hooks.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		return array(
			'wc_customer_order_xml_export_suite_auto_export_order_query_args' => array(
				'version'     => '2.0.0',
				'replacement' => 'wc_customer_order_xml_export_suite_query_args'
			),
			'wc_customer_order_xml_export_suite_order_ids' => array(
				'version'     => '2.0.0',
				'replacement' => 'wc_customer_order_xml_export_suite_ids'
			),
			'wc_customer_order_xml_export_suite_orders_exported' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'replacement' => 'wc_customer_order_xml_export_suite_order_exported'
			),
			'wc_customer_order_xml_export_suite_generated_xml' => array(
				'version'     => '2.0.0',
			),
		);

	}


	/**
	 * Return admin class instance
	 *
	 * @since 1.8.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Return cron class instance
	 *
	 * @since 1.8.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Cron
	 */
	public function get_cron_instance() {
		return $this->cron;
	}


	/**
	 * Return formats class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Formats
	 */
	public function get_formats_instance() {
		return $this->formats;
	}


	/**
	 * Return methods class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Methods
	 */
	public function get_methods_instance() {
		return $this->methods;
	}


	/**
	 * Return ajax class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Return background export class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Background_Export
	 */
	public function get_background_export_instance() {
		return $this->background_export;
	}


	/**
	 * Return batch export class instance
	 *
	 * @since 2.3.0-dev.
	 *
	 * @return \SV_WP_Job_Batch_Handler
	 */
	public function get_batch_export_instance() {
		return $this->batch_export;
	}


	/**
	 * Return download handler class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Download_Handler
	 */
	public function get_download_handler_instance() {
		return $this->download_handler;
	}


	/**
	 * Return export handler class instance
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Handler
	 */
	public function get_export_handler_instance() {
		return $this->export_handler;
	}


	/** Admin Methods ******************************************************/


	/**
	 * Render a notice for the user to select their desired export format
	 *
	 * @since 2.3.0
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {
		global $wpdb;

		// show any dependency notices
		parent::add_admin_notices();

		$loopback_enabled = $this->get_background_export_instance()->test_connection();

		// add notice for failing loopback connections
		if ( ! $loopback_enabled && $this->is_plugin_settings() ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <strong>; %2$s - </strong>; %3$s, %5$s - <a> tags; %4$s - </a> tag */
				__( '%1$sAutomated Exports%2$s are currently unavailable because your site does not support background processing. To use automated exports, please ask your hosting company to ensure your server has %3$sloopback connections%4$s enabled, or switch to a %5$srecommended hosting provider%4$s.', 'woocommerce-customer-order-xml-export-suite' ),
				'<strong>',
				'</strong>',
				'<a href="https://docs.woocommerce.com/document/ordercustomer-xml-export-suite/#faq-loopback" target="_blank">',
				'</a>',
				'<a href="https://www.skyverge.com/upgrading-php-versions/#recommended-hosts" target="_blank">'
			);

			// check $_POST to see if we've updated settings, but batch processing isn't included (meaning it's off)
			if ( ! $this->is_batch_processing_enabled() || ( isset( $_POST['wc_customer_order_xml_export_suite_orders_format'] ) && ! isset( $_POST['wc_customer_order_xml_export_suite_enable_batch_processing'] ) ) ) {

				$message .= ' ' . sprintf(
					/* translators: Placeholders: %1$s - <strong>; %2$s - </strong> */
					__( 'In the meantime, you can process manual exports by enabling the %1$sBatch Processing%2$s setting.', 'woocommerce-customer-order-xml-export-suite' ),
					'<strong>', '</strong>'
				);
			}

			$this->get_admin_notice_handler()->add_admin_notice(
				$message,
				'export-loopback-notice',
				array( 'notice_class' => 'error' )
			);
		}

		// add notice when batch processing blocks automatic exporting
		if ( $loopback_enabled && $this->is_batch_processing_enabled() && $this->is_plugin_settings() ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <strong>; %2$s - </strong>; %3$s, %5$s - <a> tags; %4$s - </a> tag */
				__( '%1$sAutomated Exports%2$s are currently unavailable because batch processing is enabled. To use automated exports, please disable batch processing and ensure your server has %3$sloopback connections%4$s enabled.', 'woocommerce-customer-order-xml-export-suite' ),
				'<strong>',
				'</strong>',
				'<a href="https://docs.woocommerce.com/document/ordercustomer-xml-export-suite/#faq-loopback" target="_blank">',
				'</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice(
				$message,
				'export-no-automatic-notice',
				array( 'notice_class' => 'error' )
			);
		}

		// add notice for mysqli requirement
		if ( ( $this->is_export_page() || $this->is_export_list_page() ) && ! $wpdb->dbh instanceof mysqli ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <a> tag; %2$s - </a> tag */
				__( 'Heads up! Your exports may consume more memory and take longer than usual unless mysqli is installed and enabled on your site. %1$sLearn More%2$s', 'woocommerce-customer-order-xml-export-suite' ),
				'<a href="https://docs.woocommerce.com/document/ordercustomer-xml-export-suite/#mysqli-streaming" target="_blank">',
				'</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice(
				$message,
				'mysqli-not-found-notice',
				array( 'dismissible' => false, 'notice_class' => 'error' )
			);
		}

		$this->add_unified_plugin_notices();
	}


	/**
	 * Adds the unified plugin install prompt.
	 *
	 * @since 2.6.3
	 */
	protected function add_unified_plugin_notices() {

		if ( ! WC_Customer_Order_XML_Export_Suite_Unified_Installer::is_installed() ) {

			$url = wp_nonce_url( add_query_arg( array( 'action' => 'wc_customer_order_xml_export_suite_install_unified_plugin' ), 'admin.php' ), $this->get_file() );

			$this->get_admin_notice_handler()->add_admin_notice( WC_Customer_Order_XML_Export_Suite_Unified_Installer::get_prompt_message( $this->get_plugin_name(), $url ), 'unified-install-prompt' );
		}
	}


	/**
	 * Installs the unified gateway plugin.
	 *
	 * Redirects and displays an error message if there were issues.
	 *
	 * @since 2.6.3
	 */
	public function install_unified_plugin() {

		$redirect_url = wp_get_referer();

		try {

			// the activation will handle redirect if successful
			WC_Customer_Order_XML_Export_Suite_Unified_Installer::install_and_activate( $redirect_url );

		} catch ( Exception $exception ) {

			$this->log( 'Could not install unified plugin. ' . $exception->getMessage() );

			$user_message = sprintf(
				__( 'Oops! It looks like an error occurred while installing the new plugin: %1$s. We recommend heading over to your %2$sWooCommerce.com account%3$s to download and install it manually.', 'woocommerce-gateway-cybersource-sop' ),
				$exception->getMessage(),
				'<a href="https://woocommerce.com/my-dashboard/">', '</a>'
			);

			$this->get_message_handler()->add_error( $user_message );
		}

		wp_safe_redirect( $redirect_url );
		exit;
	}


	/** Helper Methods ******************************************************/


	/**
	 * Main Customer/Order XML Export Suite Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.4.0
	 * @see wc_customer_order_xml_export_suite()
	 * @return WC_Customer_Order_XML_Export_Suite
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
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Customer/Order XML Export Suite', 'woocommerce-customer-order-xml-export-suite' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @param null|string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=settings' );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {

		return ( isset( $_GET['page'] ) && 'wc_customer_order_xml_export_suite' == $_GET['page'] );
	}


	/**
	 * Determines if the current page is the export list page.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_export_page() {

		return is_admin() && 'wc_customer_order_xml_export_suite' === SV_WC_Helper::get_request( 'page' ) && 'export_list' === SV_WC_Helper::get_request( 'tab' );
	}


	/**
	 * Determines if the current page is the new export page.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_export_list_page() {

		return is_admin() && 'wc_customer_order_xml_export_suite' === SV_WC_Helper::get_request( 'page' ) && 'export' === SV_WC_Helper::get_request( 'tab' );
	}


	/**
	 * Returns conditional dependencies based on the FTP security selected
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_dependencies()
	 * @return array of dependencies
	 */
	protected function get_dependencies() {

		// check if FTP is one of the chosen export methods
		if ( ! in_array( 'ftp', $this->get_auto_export_methods(), true ) ) {
			return array();
		}

		$ftp_securities = $this->get_auto_export_ftp_securities();
		$dependencies   = array();

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
	 * Returns conditional function dependencies based on the FTP security selected
	 *
	 * @since 1.2.0
	 * @see SV_WC_Plugin::get_function_dependencies()
	 * @return array of dependencies
	 */
	protected function get_function_dependencies() {

		// check if FTP is one of the chosen export methods
		if ( ! in_array( 'ftp', $this->get_auto_export_methods(), true ) ) {
			return array();
		}

		$ftp_securities = $this->get_auto_export_ftp_securities();

		if ( in_array( 'ftps', $ftp_securities, true ) ) {

			return array( 'ftp_ssl_connect' );
		}

		return array();
	}


	/**
	 * Get auto export methods used by export types
	 *
	 * @since 2.0.0
	 * @return array
	 */
	private function get_auto_export_methods() {

		$export_types   = array( 'customers', 'orders', 'coupons' );
		$export_methods = array();

		foreach ( $export_types as $export_type ) {
			$export_methods[] = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_method' );
		}

		return $export_methods;
	}


	/**
	 * Get auto export methods used by export types
	 *
	 * @since 2.0.0
	 * @return array
	 */
	private function get_auto_export_ftp_securities() {

		$export_types = array( 'customers', 'orders', 'coupons' );
		$securities   = array();

		foreach ( $export_types as $export_type ) {
			$securities[] = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_ftp_security' );
		}

		return $securities;
	}


	/**
	 * Gets the plugin documentation url
	 *
	 * @since 1.5.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.5.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Determines if batch processing is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_batch_processing_enabled() {

		// account for changes while saving settings
		if ( isset( $_POST['wc_customer_order_xml_export_suite_enable_batch_processing'] ) ) {
			return (bool) $_POST['wc_customer_order_xml_export_suite_enable_batch_processing'];
		}

		return 'yes' === get_option( 'wc_customer_order_xml_export_suite_enable_batch_processing', 'no' );
	}


	/**
	 * Determines if the option to export coupons is enabled.
	 *
	 * @since 2.5.0
	 *
	 * @return bool
	 */
	public function is_coupon_export_enabled() {

		// coupon export is compatible with WooCommerce 3.0 and above
		return SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0();
	}


	/** Lifecycle Methods ******************************************************/


	/**
	 * Install default settings
	 *
	 * @see \SV_WC_Plugin::install()
	 *
	 * @since 1.0.0
	 */
	protected function install() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-lifecycle.php' );

		WC_Customer_Order_XML_Export_Suite_Lifecycle::install();
	}


	/**
	 * Upgrades to $installed_version.
	 *
	 * @see \SV_WC_Plugin::install()
	 *
	 * @since 1.1.0
	 */
	protected function upgrade( $installed_version ) {

		require_once( $this->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-lifecycle.php' );

		WC_Customer_Order_XML_Export_Suite_Lifecycle::upgrade( $installed_version );
	}


} // end \WC_Customer_Order_XML_Export_Suite class


/**
 * Returns the One True Instance of Customer/Order XML Export Suite
 *
 * @since 1.4.0
 *
 * @return \WC_Customer_Order_XML_Export_Suite
 */
function wc_customer_order_xml_export_suite() {
	return WC_Customer_Order_XML_Export_Suite::instance();
}


// fire it up!
wc_customer_order_xml_export_suite();

} // init_woocommerce_customer_order_xml_export_suite()
