<?php
/**
 * WooCommerce Customer/Order XML Export Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Lifecycle Class
 *
 * Static class that handles installation and upgrades
 *
 * @since 2.4.0
 */
class WC_Customer_Order_XML_Export_Suite_Lifecycle {


	/**
	 * Runs install scripts.
	 *
	 * @since 2.4.0
	 */
	public static function install() {

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/data-stores/class-wc-customer-order-xml-export-suite-data-store-factory.php' );

		self::install_default_settings();
		self::install_default_custom_format_builder_settings();
		self::install_data_stores();
	}


	/**
	 * Performs upgrades from older versions.
	 *
	 * @since 2.4.0
	 *
	 * @param string $from_version current installed version
	 */
	public static function upgrade( $from_version ) {

		$plugin       = wc_customer_order_xml_export_suite();
		$upgrade_path = array(
			'1.1'   => 'upgrade_to_1_1',
			'1.1.2' => 'upgrade_to_1_1_2',
			'1.2.4' => 'upgrade_to_1_2_4',
			'2.0.0' => 'upgrade_to_2_0_0',
			'2.4.0' => 'upgrade_to_2_4_0',
			'2.5.0' => 'upgrade_to_2_5_0',
		);

		foreach ( $upgrade_path as $upgrade_to_version => $upgrade_script ) {

			if ( version_compare ( $from_version, $upgrade_to_version, '<' ) && method_exists( __CLASS__, $upgrade_script ) ) {

				$plugin->log( "Begin upgrading to version {$upgrade_to_version}..." );

				self::$upgrade_script();

				$plugin->log( "Upgrade to version {$upgrade_to_version} complete" );
			}
		}
	}


	/**
	 * Installs the database and filesystem data stores.
	 *
	 * @since 2.4.0
	 */
	public static function install_data_stores() {

		// database
		self::create_tables();

		// filesystem
		self::create_files();
	}


	/**
	 * Creates new database tables.
	 *
	 * @since 2.4.0
	 */
	public static function create_tables() {
		global $wpdb;

		WC_Customer_Order_XML_Export_Suite_Data_Store_Factory::includes( 'database' );

		// nothing to create if we're already there
		if ( self::validate_table() ) {
			return;
		}

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( WC_Customer_Order_XML_Export_Suite_Data_Store_Database::get_table_schema() );
	}


	/**
	 * Create files/directories
	 *
	 * Based on WC_Install::create_files()
	 *
	 * @since 2.4.0
	 */
	private static function create_files() {

		WC_Customer_Order_XML_Export_Suite_Data_Store_Factory::includes( 'filesystem' );

		// Install files and folders for exported files and prevent hotlinking
		$upload_dir      = WC_Customer_Order_XML_Export_Suite_Data_Store_Filesystem::get_exports_directory();
		$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = array(
			array(
				'base'    => $upload_dir,
				'file'    => 'index.html',
				'content' => ''
			),
		);

		if ( 'redirect' !== $download_method ) {
			$files[] = array(
				'base'    => $upload_dir,
				'file'    => '.htaccess',
				'content' => 'deny from all'
			);
		}

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}


	/**
	 * Validates that the table required by Customer/Order XML Export Suite is present in the database.
	 *
	 * @since 2.4.0
	 *
	 * @return bool true if all are found, false if not
	 */
	public static function validate_table() {
		global $wpdb;

		$table_name = WC_Customer_Order_XML_Export_Suite_Data_Store_Database::get_table_name();

		return $table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );
	}


	/**
	 * Installs default plugin settings.
	 *
	 * @since 2.4.0
	 *
	 * @param string|null $section_id settings section to install defaults for
	 */
	private static function install_default_settings( $section_id = null ) {

		$plugin = wc_customer_order_xml_export_suite();

		require_once( $plugin->get_plugin_path() . '/includes/admin/class-wc-customer-order-xml-export-suite-admin-settings.php' );

		foreach ( WC_Customer_Order_XML_Export_Suite_Admin_Settings::get_settings( $section_id ) as $section => $settings ) {

			foreach ( $settings as $setting ) {

				if ( isset( $setting['default'] ) ) {

					update_option( $setting['id'], $setting['default'] );
				}
			}
		}
	}


	/**
	 * Installs default custom format builder settings.
	 *
	 * @since 2.4.0
	 */
	private static function install_default_custom_format_builder_settings() {

		$plugin = wc_customer_order_xml_export_suite();

		require_once( $plugin->get_plugin_path() . '/includes/admin/class-wc-customer-order-xml-export-suite-admin-custom-format-builder.php' );

		foreach ( WC_Customer_Order_XML_Export_Suite_Admin_Custom_Format_Builder::get_settings() as $section => $settings ) {

			foreach ( $settings as $setting ) {

				if ( isset( $setting['default'] ) ) {

					update_option( $setting['id'], $setting['default'] );
				}
			}
		}
	}


	/** Upgrade Routines ******************************************************/


	/**
	 * Upgrades the plugin to version 1.1
	 *
	 * @since 2.4.0
	 */
	private static function upgrade_to_1_1() {

		// wc_customer_order_xml_export_suite_export_file_name > wc_customer_order_xml_export_suite_orders_filename
		$export_filename = get_option( 'wc_customer_order_xml_export_suite_export_file_name' );
		delete_option( 'wc_customer_order_xml_export_suite_export_file_name' );

		// wc_customer_order_xml_export_suite_auto_export_orders > wc_customer_order_xml_export_suite_auto_export_method ~ `post` > `http_post`, `no` > `disabled`
		$auto_export_method = get_option( 'wc_customer_order_xml_export_suite_auto_export_orders' );
		delete_option( 'wc_customer_order_xml_export_suite_auto_export_orders' );

		if ( 'post' === $auto_export_method ) {

			$auto_export_method = 'http_post';

		} elseif ( 'no' === $auto_export_method ) {

			$auto_export_method = 'disabled';
		}

		// wc_customer_order_xml_export_suite_auto_export_pending, etc > wc_customer_order_xml_export_suite_auto_export_statuses ~ simple array of order statuses to include in export
		$order_statuses = array( 'pending', 'on-hold', 'processing', 'completed', 'failed', 'cancelled', 'refunded' );
		foreach ( $order_statuses as $key => $order_status ) {

			$option_key = "wc_customer_order_xml_export_suite_auto_export_{$order_status}";

			if ( 'no' === get_option( $option_key ) ) {
				unset( $order_statuses[ $key ] );
			}

			delete_option( $option_key );
		}

		// wc_customer_order_xml_export_suite_log_errors (yes/no) > wc_customer_order_xml_export_suite_debug_mode (on/off)
		$debug_mode = ( 'yes' === get_option( 'wc_customer_order_xml_export_suite_log_errors' ) ) ? 'on' : 'off';
		delete_option( 'wc_customer_order_xml_export_suite_log_errors' );

		// initial path wc_customer_order_xml_export_suite_ftp_initial_path > wc_customer_order_xml_export_suite_ftp_path
		$initial_path = get_option( 'wc_customer_order_xml_export_suite_ftp_initial_path' );
		delete_option( 'wc_customer_order_xml_export_suite_ftp_initial_path' );

		// add new options
		update_option( 'wc_customer_order_xml_export_suite_orders_filename', $export_filename );
		update_option( 'wc_customer_order_xml_export_suite_auto_export_method', $auto_export_method );
		update_option( 'wc_customer_order_xml_export_suite_auto_export_statuses', $order_statuses );
		update_option( 'wc_customer_order_xml_export_suite_debug_mode', $debug_mode );
		update_option( 'wc_customer_order_xml_export_suite_ftp_path', $initial_path );
	}


	/**
	 * Upgrades the plugin to version 1.1.2
	 *
	 * @since 2.4.0
	 */
	private static function upgrade_to_1_1_2() {

		// wc_customer_order_xml_export_suite_passive_mode > wc_customer_order_xml_export_suite_ftp_passive_mode
		update_option( 'wc_customer_order_xml_export_suite_ftp_passive_mode', get_option( 'wc_customer_order_xml_export_suite_passive_mode' ) );
		delete_option( 'wc_customer_order_xml_export_suite_passive_mode' );
	}


	/**
	 * Upgrades the plugin to version 1.2.4
	 *
	 * @since 2.4.0
	 */
	private static function upgrade_to_1_2_4() {

		// update order statuses for 2.2+
		$order_status_options = array( 'wc_customer_order_xml_export_suite_statuses', 'wc_customer_order_xml_export_suite_auto_export_statuses' );

		foreach ( $order_status_options as $option ) {

			$order_statuses     = (array) get_option( $option );
			$new_order_statuses = array();

			foreach ( $order_statuses as $status ) {
				$new_order_statuses[] = 'wc-' . $status;
			}

			update_option( $option, $new_order_statuses );
		}
	}


	/**
	 * Upgrades the plugin to version 2.0.0
	 *
	 * @since 2.4.0
	 */
	private static function upgrade_to_2_0_0() {

		// install defaults for customer auto-export settings, this must be done before
		// updating renamed options, otherwise defaults will override the previously set options
		self::install_default_settings();

		// set up xml exports folder
		self::create_files();

		// install defaults for new settings
		update_option( 'wc_customer_order_xml_export_suite_orders_add_note', 'yes' );
		update_option( 'wc_customer_order_xml_export_suite_orders_auto_export_trigger', 'schedule' );

		// make sure existing installations use legacy format, so that the upgrade doesn't break anything
		update_option( 'wc_customer_order_xml_export_suite_orders_format', 'legacy' );
		update_option( 'wc_customer_order_xml_export_suite_customers_format', 'legacy' );

		// rename settings
		$renamed_options = array(
			'wc_customer_order_xml_export_suite_auto_export_method'     => 'wc_customer_order_xml_export_suite_orders_auto_export_method',
			'wc_customer_order_xml_export_suite_auto_export_start_time' => 'wc_customer_order_xml_export_suite_orders_auto_export_start_time',
			'wc_customer_order_xml_export_suite_auto_export_interval'   => 'wc_customer_order_xml_export_suite_orders_auto_export_interval',
			'wc_customer_order_xml_export_suite_auto_export_statuses'   => 'wc_customer_order_xml_export_suite_orders_auto_export_statuses',
			'wc_customer_order_xml_export_suite_ftp_server'             => 'wc_customer_order_xml_export_suite_orders_ftp_server',
			'wc_customer_order_xml_export_suite_ftp_username'           => 'wc_customer_order_xml_export_suite_orders_ftp_username',
			'wc_customer_order_xml_export_suite_ftp_password'           => 'wc_customer_order_xml_export_suite_orders_ftp_password',
			'wc_customer_order_xml_export_suite_ftp_port'               => 'wc_customer_order_xml_export_suite_orders_ftp_port',
			'wc_customer_order_xml_export_suite_ftp_path'               => 'wc_customer_order_xml_export_suite_orders_ftp_path',
			'wc_customer_order_xml_export_suite_ftp_security'           => 'wc_customer_order_xml_export_suite_orders_ftp_security',
			'wc_customer_order_xml_export_suite_ftp_passive_mode'       => 'wc_customer_order_xml_export_suite_orders_ftp_passive_mode',
			'wc_customer_order_xml_export_suite_http_post_url'          => 'wc_customer_order_xml_export_suite_orders_http_post_url',
			'wc_customer_order_xml_export_suite_email_recipients'       => 'wc_customer_order_xml_export_suite_orders_email_recipients',
			'wc_customer_order_xml_export_suite_email_subject'          => 'wc_customer_order_xml_export_suite_orders_email_subject',
		);

		foreach ( $renamed_options as $old => $new ) {

			update_option( $new, get_option( $old ) );
			delete_option( $old );
		}

		// install default custom column format settings
		self::install_default_custom_format_builder_settings();

		// handle renamed cron schedule
		if ( $start_timestamp = wp_next_scheduled( 'wc_customer_order_xml_export_suite_auto_export_interval' ) ) {

			wp_clear_scheduled_hook( 'wc_customer_order_xml_export_suite_auto_export_interval' );

			wp_schedule_event( $start_timestamp, 'wc_customer_order_xml_export_suite_orders_auto_export_interval', 'wc_customer_order_xml_export_suite_auto_export_orders' );
		}
	}


	/**
	 * Upgrades the plugin to version 2.4.0
	 *
	 * @since 2.4.0
	 */
	private static function upgrade_to_2_4_0() {

		self::create_tables();
	}


	/**
	 * Upgrades the plugin to version 2.5.0.
	 *
	 * @since 2.5.0
	 */
	private static function upgrade_to_2_5_0() {

		$plugin = wc_customer_order_xml_export_suite();

		// set default settings for coupons export
		self::install_default_settings( 'coupons' );

		// set default mapping for custom coupons export
		$custom_format = $plugin->get_formats_instance()->get_format( 'coupons', 'default' );

		$mapping = array();
		foreach ( $custom_format['fields'] as $column => $name ) {
			$mapping[] = array( 'source' => $column, 'name' => $name );
		}

		update_option( 'wc_customer_order_xml_export_suite_coupons_custom_format_mapping', $mapping );

		// remove debug mode setting
		delete_option( 'wc_customer_order_xml_export_suite_debug_mode' );
	}

}
