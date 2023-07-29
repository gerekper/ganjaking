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

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats\CSV\Coupons_Export_Format_Definition;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats\CSV\Customers_Export_Format_Definition;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats\CSV\Orders_Export_Format_Definition;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats;
use SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order CSV Export Lifecycle Class
 *
 * Static class that handles installation and upgrades
 *
 * @since 4.5.0
 *
 * @method \WC_Customer_Order_CSV_Export get_plugin()
 */
class WC_Customer_Order_CSV_Export_Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 5.3.0
	 *
	 * @param \WC_Customer_Order_CSV_Export $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'3.0.4',
			'3.4.0',
			'3.12.0',
			'4.0.0',
			'4.5.0',
			'4.6.4',
			'4.7.0',
			'4.8.0',
			'5.0.0',
			'5.3.0',
		];
	}


	/**
	 * Adds any action or filter hooks.
	 *
	 * @since 5.0.5
	 */
	protected function add_hooks() {

		parent::add_hooks();

		// deactivate the XML Export suite whenever this plugin is active
		add_action( 'admin_init', function() {

			if ( get_option( 'wc_customer_order_export_migrated_from_xml_export_suite' ) && is_plugin_active( 'woocommerce-customer-order-xml-export-suite/woocommerce-customer-order-xml-export-suite.php' ) && ! apply_filters( 'wc_customer_order_export_allow_legacy_xml_export', false ) ) {
				deactivate_plugins( 'woocommerce-customer-order-xml-export-suite/woocommerce-customer-order-xml-export-suite.php' );
			}

		} );

		// run a routine for migrating existing export data to the new format & tables
		add_action( 'wc_customer_order_export_migrate_xml_exports', [ $this, 'migrate_xml_exports' ] );
	}


	/**
	 * Runs install scripts.
	 *
	 * @since 4.5.0
	 */
	public function install() {

		require_once( $this->get_plugin()->get_plugin_path() . '/src/data-stores/class-wc-customer-order-csv-export-data-store-factory.php' );

		self::install_data_stores();

		// if explicitly told to migrate
		if ( get_option( 'wc_customer_order_export_migrate_from_xml' ) ) {

			$this->migrate_from_xml();

			delete_option( 'wc_customer_order_export_migrate_from_xml' );

		// otherwise if XML Export is installed, offer a notice
		} elseif ( get_option( 'wc_customer_order_xml_export_suite_version', false ) ) {

			update_option( 'wc_customer_order_export_offer_xml_migration', 'yes' );
		}
	}


	/**
	 * Gets the currently installed plugin version.
	 *
	 * Overrides this function to support retrieving the version from the old plugin ID.
	 *
	 * @see LifeCycle::get_installed_version()
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	protected function get_installed_version() {

		$version = get_option( $this->get_plugin()->get_plugin_version_name() );

		if ( empty( $version ) ) {

			// try to retrieve it from the old ID
			$option_name = 'wc_customer_order_csv_export_version';
			$version     = get_option( $option_name );
		}

		return $version;
	}


	/**
	 * Installs the database and filesystem data stores.
	 *
	 * @since 4.5.0
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
	 * @since 4.5.0
	 */
	public static function create_tables() {
		global $wpdb;

		WC_Customer_Order_CSV_Export_Data_Store_Factory::includes( 'database' );

		// nothing to create if we're already there
		if ( self::validate_table() ) {
			return;
		}

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( WC_Customer_Order_CSV_Export_Data_Store_Database::get_table_schema() );
	}


	/**
	 * Create files/directories
	 *
	 * Based on WC_Install::create_files()
	 *
	 * @since 4.5.0
	 */
	private static function create_files() {

		WC_Customer_Order_CSV_Export_Data_Store_Factory::includes( 'filesystem' );

		// Install files and folders for exported files and prevent hotlinking
		$upload_dir      = WC_Customer_Order_CSV_Export_Data_Store_Filesystem::get_exports_directory();
		$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = [
			[
				'base'    => $upload_dir,
				'file'    => 'index.html',
				'content' => ''
			],
		];

		if ( 'redirect' !== $download_method ) {
			$files[] = [
				'base'    => $upload_dir,
				'file'    => '.htaccess',
				'content' => 'deny from all'
			];
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
	 * Validates that the table required by Customer/Order CSV Export is present in the database.
	 *
	 * @since 4.5.0
	 *
	 * @return bool true if all are found, false if not
	 */
	public static function validate_table() {
		global $wpdb;

		$table_name = WC_Customer_Order_CSV_Export_Data_Store_Database::get_table_name();

		return $table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );
	}


	/**
	 * Installs default plugin settings for a given section.
	 *
	 * @since 4.7.0
	 */
	public function install_section_default_settings() {

		wc_deprecated_function( __METHOD__, '5.0.0' );
	}


	/** Upgrade Routines ******************************************************/


	/**
	 * Upgrades the plugin to version 3.0.4
	 *
	 * @since 4.5.0
	 */
	private function upgrade_to_3_0_4() {

		// wc_customer_order_csv_export_passive_mode > wc_customer_order_csv_export_ftp_passive_mode
		update_option( 'wc_customer_order_csv_export_ftp_passive_mode', get_option( 'wc_customer_order_csv_export_passive_mode' ) );
		delete_option( 'wc_customer_order_csv_export_passive_mode' );
	}


	/**
	 * Upgrades the plugin to version 3.4.0
	 *
	 * @since 4.5.0
	 */
	private function upgrade_to_3_4_0() {

		// update order statuses for 2.2+
		$order_status_options = [ 'wc_customer_order_csv_export_statuses', 'wc_customer_order_csv_export_auto_export_statuses' ];

		foreach ( $order_status_options as $option ) {

			$order_statuses     = (array) get_option( $option );
			$new_order_statuses = [];

			foreach ( $order_statuses as $status ) {
				$new_order_statuses[] = 'wc-' . $status;
			}

			update_option( $option, $new_order_statuses );
		}
	}


	/**
	 * Upgrades the plugin to version 3.12.0
	 *
	 * @since 4.5.0
	 */
	private function upgrade_to_3_12_0() {

		if ( 'import' === get_option( 'wc_customer_order_csv_export_order_format' ) ) {
			update_option( 'wc_customer_order_csv_export_order_format', 'legacy_import' );
		}
	}


	/**
	 * Upgrades the plugin to version 4.0.0
	 *
	 * @since 4.5.0
	 */
	private function upgrade_to_4_0_0() {

		$plugin = $this->get_plugin();

		self::create_files();

		// install defaults for new settings
		update_option( 'wc_customer_order_csv_export_orders_add_note', 'yes' );
		update_option( 'wc_customer_order_csv_export_orders_auto_export_trigger', 'schedule' );

		// rename settings
		$renamed_options = [
			'wc_customer_order_csv_export_order_format'           => 'wc_customer_order_csv_export_orders_format',
			'wc_customer_order_csv_export_order_filename'         => 'wc_customer_order_csv_export_orders_filename',
			'wc_customer_order_csv_export_customer_format'        => 'wc_customer_order_csv_export_customers_format',
			'wc_customer_order_csv_export_customer_filename'      => 'wc_customer_order_csv_export_customers_filename',
			'wc_customer_order_csv_export_auto_export_method'     => 'wc_customer_order_csv_export_orders_auto_export_method',
			'wc_customer_order_csv_export_auto_export_start_time' => 'wc_customer_order_csv_export_orders_auto_export_start_time',
			'wc_customer_order_csv_export_auto_export_interval'   => 'wc_customer_order_csv_export_orders_auto_export_interval',
			'wc_customer_order_csv_export_auto_export_statuses'   => 'wc_customer_order_csv_export_orders_auto_export_statuses',
			'wc_customer_order_csv_export_ftp_server'             => 'wc_customer_order_csv_export_orders_ftp_server',
			'wc_customer_order_csv_export_ftp_username'           => 'wc_customer_order_csv_export_orders_ftp_username',
			'wc_customer_order_csv_export_ftp_password'           => 'wc_customer_order_csv_export_orders_ftp_password',
			'wc_customer_order_csv_export_ftp_port'               => 'wc_customer_order_csv_export_orders_ftp_port',
			'wc_customer_order_csv_export_ftp_path'               => 'wc_customer_order_csv_export_orders_ftp_path',
			'wc_customer_order_csv_export_ftp_security'           => 'wc_customer_order_csv_export_orders_ftp_security',
			'wc_customer_order_csv_export_ftp_passive_mode'       => 'wc_customer_order_csv_export_orders_ftp_passive_mode',
			'wc_customer_order_csv_export_http_post_url'          => 'wc_customer_order_csv_export_orders_http_post_url',
			'wc_customer_order_csv_export_email_recipients'       => 'wc_customer_order_csv_export_orders_email_recipients',
			'wc_customer_order_csv_export_email_subject'          => 'wc_customer_order_csv_export_orders_email_subject',
		];

		foreach ( $renamed_options as $old => $new ) {

			update_option( $new, get_option( $old ) );
			delete_option( $old );
		}

		// maintain backwards compatibility with previous `default` and
		// `default_one_row_per_item` formats for those who use it by creating a custom
		// format based on the previous version
		$orders_format = get_option( 'wc_customer_order_csv_export_orders_format' );

		if ( in_array( $orders_format, [ 'default', 'default_one_row_per_item' ], true ) ) {

			$custom_format = $plugin->get_formats_instance()->get_format( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $orders_format );

			// keep order_number backwards-compatible and remove refunds key
			$custom_format['columns']['order_number_formatted'] = 'order_number';
			unset( $custom_format['columns']['order_number'], $custom_format['columns']['refunds'] );

			if ( 'default_one_row_per_item' === $orders_format ) {

				// rename 'total_tax' back to 'tax'
				$custom_format['columns']['total_tax'] = 'tax';

				// remove item-specific keys that weren't present in the old default format
				unset(
					$custom_format['columns']['item_id'],
					$custom_format['columns']['item_product_id'],
					$custom_format['columns']['subtotal'],
					$custom_format['columns']['subtotal_tax']
				);

				update_option( 'wc_customer_order_csv_export_orders_custom_format_row_type', 'item' );

			} else {

				update_option( 'wc_customer_order_csv_export_orders_custom_format_row_type', 'order' );
			}

			$mapping = [];

			foreach ( $custom_format['columns'] as $column => $name ) {
				$mapping[] = [ 'source' => $column, 'name' => $name ];
			}

			update_option( 'wc_customer_order_csv_export_orders_custom_format_delimiter', ',' );
			update_option( 'wc_customer_order_csv_export_orders_custom_format_mapping', $mapping );

			// set the current orders export format as `custom`
			update_option( 'wc_customer_order_csv_export_orders_format', 'custom' );
		}
	}


	/**
	 * Upgrades the plugin to version 4.5.0
	 *
	 * @since 4.5.0
	 */
	private function upgrade_to_4_5_0() {

		self::create_tables();
	}


	/**
	 * Updates to v4.6.4
	 *
	 * @since 4.6.4
	 */
	private function upgrade_to_4_6_4() {

		// set a flag to keep legacy import formats for existing installs only
		update_option( 'wc_customer_order_csv_export_keep_legacy_formats', 'yes' );
	}


	/**
	 * Updates to v4.7.0
	 *
	 * @since 4.7.0
	 */
	private function upgrade_to_4_7_0() {

		$plugin = $this->get_plugin();

		// load custom orders format from old options
		$row_type             = get_option( 'wc_customer_order_csv_export_orders_custom_format_row_type', 'order' );
		$items_format         = 'order' === $row_type ? get_option( 'wc_customer_order_csv_export_orders_custom_format_items_format', 'pipe_delimited' ) : 'pipe_delimited';
		$custom_orders_format = new Orders_Export_Format_Definition( [
			'name'             => 'Custom',
			'key'              => 'custom',
			'delimiter'        => get_option( 'wc_customer_order_csv_export_orders_custom_format_delimiter', ',' ),
			'enclosure'        => '"',
			'row_type'         => $row_type,
			'items_format'     => $items_format,
			'mapping'          => get_option( 'wc_customer_order_csv_export_orders_custom_format_mapping', [] ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_csv_export_orders_custom_format_include_all_meta' ),
		] );

		// save it to the new single option
		$plugin->get_formats_instance()->save_custom_format( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $custom_orders_format );

		// load custom customers format from old options
		$custom_customers_format = new Customers_Export_Format_Definition( [
			'name'             => 'Custom',
			'key'              => 'custom',
			'delimiter'        => get_option( 'wc_customer_order_csv_export_customers_custom_format_delimiter', ',' ),
			'enclosure'        => '"',
			'mapping'          => get_option( 'wc_customer_order_csv_export_customers_custom_format_mapping', [] ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_csv_export_customers_custom_format_include_all_meta' ),
		] );

		// save it to the new single option
		$plugin->get_formats_instance()->save_custom_format( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, $custom_customers_format );

		// load custom coupons format from old options
		$custom_coupons_format = new Coupons_Export_Format_Definition( [
			'name'             => 'Custom',
			'key'              => 'custom',
			'delimiter'        => get_option( 'wc_customer_order_csv_export_coupons_custom_format_delimiter', ',' ),
			'enclosure'        => '"',
			'mapping'          => get_option( 'wc_customer_order_csv_export_coupons_custom_format_mapping', [] ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_csv_export_coupons_custom_format_include_all_meta' ),
		] );

		// save it to the new single option
		$plugin->get_formats_instance()->save_custom_format( WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS, $custom_coupons_format );
	}


	/**
	 * Updates to v4.8.0
	 *
	 * @since 4.8.0
	 */
	private function upgrade_to_4_8_0() {

		// leading space is intentional
		if ( ' default' === get_option( 'wc_customer_order_csv_export_orders_format' ) ) {
			update_option( 'wc_customer_order_csv_export_orders_format', 'default' );
		}
	}


	/**
	 * Updates to v5.0.0
	 *
	 * @since 5.0.0
	 */
	private function upgrade_to_5_0_0() {
		global $wpdb;

		// rename general options
		$renamed_options = [
			// old name => new name
			'wc_customer_order_csv_export_version'                 => 'wc_customer_order_export_version',
			'wc_customer_order_csv_export_milestone_version'       => 'wc_customer_order_export_milestone_version',
			'wc_customer_order_csv_export_lifecycle_events'        => 'wc_customer_order_export_lifecycle_events',
			'wc_customer_order_csv_export_enable_batch_processing' => 'wc_customer_order_export_enable_batch_processing',
			'wc_customer_order_csv_export_keep_legacy_formats'     => 'wc_customer_order_export_keep_legacy_formats',
		];

		foreach ( $renamed_options as $old_option => $new_option ) {

			$wpdb->update(
				$wpdb->options,
				// new name
				[ 'option_name' => $new_option ],
				// old name
				[ 'option_name' => $old_option ]
			);
		}

		$this->migrate_custom_formats();

		$this->migrate_export_jobs( 'wc_customer_order_csv_export_background_export_job_' );

		$this->migrate_metadata( '_wc_customer_order_csv_export' );

		// rename user meta
		$wpdb->update(
			$wpdb->usermeta,
			// new key
			[ 'meta_key' => '_wc_customer_order_export_notices' ],
			// old key
			[ 'meta_key' => '_wc_customer_order_csv_export_notices' ]
		);

		// migrate the CSV Export automations
		$this->migrate_automations( 'wc_customer_order_csv_export', \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV );

		// set option to display admin notice
		update_option( 'wc_' . $this->get_plugin()->get_id() . '_upgraded', 'yes' );
	}


	/**
	 * Updates to v5.3.0
	 *
	 * This release merges a couple of free add ons into the core plugin.
	 *
	 * @since 5.3.0
	 */
	protected function upgrade_to_5_3_0() {

		$free_add_ons = [
			'woocommerce-order-export-refunds-only' => 'woocommerce-order-export-refunds-only/woocommerce-order-export-refunds-only.php',
			'woocommerce-order-export-vat'          => 'woocommerce-order-export-vat/woocommerce-order-export-vat.php',
		];

		foreach ( array_keys( $free_add_ons ) as $plugin_id ) {
			if ( ! $this->get_plugin()->is_plugin_active( $plugin_id . '.php' ) ) {
				unset( $free_add_ons[ $plugin_id ] );
			}
		}

		// if the free add ons are found to be active, deactivate them
		if ( ! empty( $free_add_ons ) ) {

			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			deactivate_plugins( array_values( $free_add_ons ) );

			update_option( 'wc_customer_order_export_free_add_ons_migrated', array_keys( $free_add_ons ) );
		}

		delete_option( 'wc_order_export_refunds_only_version' );
		delete_option( 'wc_order_export_VAT_version' );
	}


	/**
	 * Migrates custom formats.
	 *
	 * @since 5.0.0
	 */
	private function migrate_custom_formats() {

		$sections = [
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS,
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS,
		];

		foreach ( $sections as $section ) {

			// add output type to existing CSV custom formats
			$custom_formats = get_option( 'wc_customer_order_csv_export_' . $section . '_custom_formats', [] );

			foreach ( $custom_formats as $custom_format_key => $custom_format_data ) {
				$custom_formats[ $custom_format_key ]['output_type'] = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV;
			}

			update_option( 'wc_customer_order_export_' . $section . '_custom_formats', $custom_formats );
		}
	}


	/**
	 * Migrates export job option names to the new format.
	 *
	 * @since 5.0.0
	 *
	 * @param string $from_prefix job option name prefix
	 */
	private function migrate_export_jobs( $from_prefix ) {
		global $wpdb;

		// rename jobs
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $wpdb->options
				SET option_name = REPLACE(
					option_name,
					%s,
					%s
				)
				WHERE option_name LIKE %s",
				$from_prefix,
				'wc_customer_order_export_background_export_job_',
				$wpdb->esc_like( "{$from_prefix}_%" )
			)
		);
	}


	/**
	 * Migrates object metadata.
	 *
	 * @since 5.0.0
	 *
	 * @param string $key_prefix meta key prefix
	 */
	private function migrate_metadata( string $key_prefix ): void {
		global $wpdb;

		if ( Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ) {
			$order_meta_table = OrdersTableDataStore::get_meta_table_name();
			$order_id_column  = 'order_id';
		} else {
			$order_meta_table = $wpdb->postmeta;
			$order_id_column  = 'post_id';
		}

		// migrate exported orders meta
		$this->migrate_exported_status_metadata( $order_meta_table, $order_id_column, "{$key_prefix}_is_exported", Taxonomies_Handler::TAXONOMY_NAME_ORDERS );
		// migrate exported guest customers meta
		$this->migrate_exported_status_metadata( $order_meta_table, $order_id_column, "{$key_prefix}_customer_is_exported", Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER );
		// migrate exported users meta
		$this->migrate_exported_status_metadata( $wpdb->usermeta, 'user_id', "{$key_prefix}_is_exported", Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );
	}


	/**
	 * Migrates existing export status metadata to terms.
	 *
	 * @since 5.0.0
	 *
	 * @param string $meta_table metadata table to search
	 * @param string $id_key metadata table object ID column name
	 * @param string $meta_key metadata key to look for
	 * @param string $taxonomy export status taxonomy to add term
	 */
	private function migrate_exported_status_metadata( string $meta_table, string $id_key, string $meta_key, string $taxonomy ): void {
		global $wpdb;

		// get the exported object (order/user) IDs
		$object_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT {$id_key} FROM {$meta_table} WHERE meta_key = %s AND meta_value = 1",
				$meta_key
			)
		);

		// mark each object as globally exported
		if ( ! empty( $object_ids ) && $exported_term = get_term_by( 'slug', Taxonomies_Handler::GLOBAL_TERM, $taxonomy ) ) {

			foreach ( $object_ids as $object_id ) {

				$wpdb->insert( $wpdb->term_relationships, [
					'object_id'        => $object_id,
					'term_taxonomy_id' => $exported_term->term_taxonomy_id,
					'term_order'       => 0
				] );
			}
		}
	}


	/**
	 * Migrates existing automation settings into the new automation objects storage.
	 *
	 * This will take the current options for each automation method (FTP, email, etc...) and create an automation
	 * object if it is minimally configured. Only the method that is currently enabled in the settings will be switched
	 * to enabled. The rest will be created, but disabled to preserve any previously entered settings.
	 *
	 * e.g. if there are FTP credentials configured, but the user is not currently exporting via FTP, we'll still create
	 * a disabled automation for FTP so they don't have to re-enter credentials if they choose to switch back to it.
	 *
	 * @since 5.0.0
	 *
	 * @param string $option_prefix prefix for the concrete automation settings options
	 * @param string $output_type output type, such as csv or xml
	 * @param string $custom_format_key the custom format key to use if the legacy automation is set to a "Custom" format
	 */
	private function migrate_automations( $option_prefix, $output_type, $custom_format_key = null ) {

		// note: coupons do not have automations...yet
		$export_types = [
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS,
		];

		foreach ( $export_types as $export_type ) {

			$format               = get_option( "{$option_prefix}_{$export_type}_format", 'default' );
			$filename             = get_option( "{$option_prefix}_{$export_type}_filename", '' );
			$add_notes            = 'yes' === get_option( "{$option_prefix}_{$export_type}_add_note" );
			$method               = get_option( "{$option_prefix}_{$export_type}_auto_export_method", 'disabled' );
			$action               = 'schedule' === get_option( "{$option_prefix}_{$export_type}_auto_export_trigger", 'schedule' ) ? 'interval' : 'immediate';
			$interval             = (int) get_option( "{$option_prefix}_{$export_type}_auto_export_interval", '' );
			$start                = get_option( "{$option_prefix}_{$export_type}_auto_export_start_time", '' );
			$statuses             = get_option( "{$option_prefix}_{$export_type}_auto_export_statuses", [] );
			$product_ids          = $this->format_option_ids( get_option( "{$option_prefix}_{$export_type}_auto_export_products", [] ) );
			$product_category_ids = $this->format_option_ids( get_option( "{$option_prefix}_{$export_type}_auto_export_product_categories", [] ) );

			// if the format is set to "Custom", either use the provided custom format key or try and get the legacy option
			if ( 'custom' === $format ) {
				$format = $custom_format_key ?: get_option( "{$option_prefix}_{$export_type}_custom_format", 'custom' );
			}

			if ( ! is_array( $statuses ) ) {
				$statuses = [];
			}

			$interval = $interval ? $interval * MINUTE_IN_SECONDS : '';
			$last_run = '';

			// grab the next scheduled cron even if it exists, and consider that the start
			if ( $next_scheduled = wp_next_scheduled( "{$option_prefix}_auto_export_{$export_type}" ) ) {

				wp_unschedule_hook( "{$option_prefix}_auto_export_{$export_type}" );

				$start    = $next_scheduled;
				$last_run = $next_scheduled - $interval;

			} elseif ( $start ) {

				$current_time = current_time( 'timestamp' );
				$start        = strtotime( 'today ' . $start, $current_time );

				if ( $current_time > $start ) {
					$start = strtotime( 'tomorrow ' . $start, $current_time );
				}

				$start -= ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			}

			$automation_args = [
				'output_type'          => $output_type,
				'export_type'          => $export_type,
				'format_key'           => $format,
				'filename'             => $filename,
				'add_notes'            => $add_notes,
				'action'               => $action,
				'interval'             => $interval,
				'start'                => $start,
				'last_run'             => $last_run,
				'statuses'             => $statuses,
				'product_ids'          => $product_ids,
				'product_category_ids' => $product_category_ids,
			];

			// handle the FTP automation settings
			$ftp_server   = get_option( "{$option_prefix}_{$export_type}_ftp_server", '' );
			$ftp_username = get_option( "{$option_prefix}_{$export_type}_ftp_username", '' );
			$ftp_password = get_option( "{$option_prefix}_{$export_type}_ftp_password", '' );

			// if the FTP method is minimally configured, add it as an automation
			if ( $ftp_server && $ftp_username && $ftp_password ) {

				$args = array_merge( $automation_args, [
					'enabled'              => 'ftp' === $method, // only enable if currently chosen in the settings
					'name'                 => 'Export via FTP',
					'method_type'          => 'ftp',
					'method_settings'      => [
						'ftp_server'       => $ftp_server,
						'ftp_username'     => $ftp_username,
						'ftp_password'     => $ftp_password,
						'ftp_port'         => get_option( "{$option_prefix}_{$export_type}_ftp_port", '' ),
						'ftp_path'         => get_option( "{$option_prefix}_{$export_type}_ftp_path", '' ),
						'ftp_security'     => get_option( "{$option_prefix}_{$export_type}_ftp_security", 'none' ),
						'ftp_passive_mode' => 'yes' === get_option( "{$option_prefix}_{$export_type}_ftp_passive_mode", 'no' ) ? 'yes' : 'no',
					],
				] );

				$this->create_automation( $args );
			}

			// HTTP POST method
			if ( $http_post_url = get_option( "{$option_prefix}_{$export_type}_http_post_url", '' ) ) {

				$args = array_merge( $automation_args, [
					'enabled'              => 'http_post' === $method, // only enable if currently chosen in the settings
					'name'                 => 'Export via HTTP POST',
					'method_type'          => 'http_post',
					'method_settings'      => [
						'http_post_url' => $http_post_url,
					],
				] );

				$this->create_automation( $args );
			}

			// Email method
			if ( $email_subject = get_option( "{$option_prefix}_{$export_type}_email_subject", '' ) ) {

				$args = array_merge( $automation_args, [
					'enabled'              => 'email' === $method, // only enable if currently chosen in the settings
					'name'                 => 'Export via Email',
					'method_type'          => 'email',
					'method_settings'      => [
						'email_recipients' => get_option( "{$option_prefix}_{$export_type}_email_recipients", '' ),
						'email_subject'    => $email_subject,
					],
				] );

				$this->create_automation( $args );
			}

			// add a dedicated local automation
			if ( 'local' === $method ) {

				$args = array_merge( $automation_args, [
					'enabled'              => true,
					'name'                 => 'Export Locally',
					'method_type'          => 'local',
				] );

				$this->create_automation( $args );
			}
		}
	}


	/**
	 * Creates an automation based on the given args.
	 *
	 * @since 5.0.0
	 *
	 * @param array $args automation args
	 */
	private function create_automation( array $args = [] ) {

		$args = wp_parse_args( $args, [
			'enabled'              => false,
			'name'                 => 'Automated Export',
			'output_type'          => '',
			'export_type'          => '',
			'format_key'           => '',
			'filename'             => '',
			'add_notes'            => false,
			'method_type'          => '',
			'method_settings'      => [],
			'action'               => '',
			'interval'             => 30 * MINUTE_IN_SECONDS,
			'start'                => '',
			'last_run'             => null,
			'statuses'             => [],
			'product_ids'          => [],
			'product_category_ids' => [],
		] );

		try {

			$automation = new \SkyVerge\WooCommerce\CSV_Export\Automations\Automation();
			$automation->set_enabled( $args['enabled'] );
			$automation->set_name( $args['name'] );
			$automation->set_output_type( $args['output_type'] );
			$automation->set_export_type( $args['export_type'] );
			$automation->set_format_key( $args['format_key'] );
			$automation->set_filename( $args['filename'] );
			$automation->set_add_notes( $args['add_notes'] );
			$automation->set_method_type( $args['method_type'] );
			$automation->set_method_settings( $args['method_settings'] );
			$automation->set_action( $args['action'] );
			$automation->set_interval( $args['interval'] );
			$automation->set_start( $args['start'] );
			$automation->set_last_run( $args['last_run'] );
			$automation->set_statuses( $args['statuses'] );
			$automation->set_product_ids( $args['product_ids'] );
			$automation->set_product_category_ids( $args['product_category_ids'] );

			$automation->save();

			// record any active automation ID for its output & export type
			if ( $automation->is_enabled() && $automation->get_output_type() && $automation->get_export_type() ) {
				update_option( "wc_customer_order_export_migrated_active_{$automation->get_output_type()}_{$automation->get_export_type()}_automation_id", $automation->get_id() );
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$this->get_plugin()->log( 'Could not migrate automation settings. ' . $exception->getMessage() );
		}
	}


	/**
	 * Formats a list of IDs from an option into a sanitized array.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array $value IDs, either in a comma separated list or array
	 * @return array
	 */
	private function format_option_ids( $value ) {

		if ( is_string( $value ) ) {
			$value = explode( ',', $value );
		} else if ( ! is_array( $value ) ) {
			$value = [];
		}

		return array_filter( array_map( 'absint', $value ) );
	}


	/**
	 * Migrates XML Export plugin data.
	 *
	 * @since 5.0.5
	 */
	public function migrate_from_xml() {

		// XML export never removed legacy formats, so migrated XML users should see them
		update_option( 'wc_customer_order_export_keep_legacy_formats', 'yes' );

		$this->migrate_xml_custom_formats();

		$this->get_plugin()->log( 'Migrating XML Export automations' );

		$this->migrate_automations( 'wc_customer_order_xml_export_suite', \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML, 'custom-migrated-from-xml-export-suite' );

		$this->get_plugin()->log( 'Migrating XML Export meta data' );

		$this->migrate_metadata( '_wc_customer_order_xml_export_suite' );

		// schedule a future one-off action to migrate the export records in the background, since that could be too heavy for this main routine
		as_schedule_single_action( time() + 30, 'wc_customer_order_export_migrate_xml_exports' );

		update_option( 'wc_customer_order_export_migrated_from_xml_export_suite', 'yes' );
	}


	/**
	 * Migrates the XML Export custom formats.
	 *
	 * XML Export never got the "multiple custom formats" feature, so we only need to convert the legacy settings for each export type.
	 *
	 * @since 5.0.5
	 */
	private function migrate_xml_custom_formats() {

		$this->get_plugin()->log( 'Migrating XML Export custom formats' );

		$custom_format_key = 'custom-migrated-from-xml-export-suite';

		$custom_orders_format = new Export_Formats\XML\Orders_Export_Format_Definition( [
			'name'             => 'Custom - Migrated from XML Export Suite',
			'key'              => $custom_format_key,
			'indent'           => get_option( 'wc_customer_order_xml_export_suite_orders_custom_format_indent', false ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_xml_export_suite_orders_custom_format_include_all_meta' ),
			'mapping'          => get_option( 'wc_customer_order_xml_export_suite_orders_custom_format_mapping', [] ),
		] );

		wc_customer_order_csv_export()->get_formats_instance()->save_custom_format( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $custom_orders_format );

		$custom_customers_format = new Export_Formats\XML\Customers_Export_Format_Definition( [
			'name'             => 'Custom - Migrated from XML Export Suite',
			'key'              => $custom_format_key,
			'indent'           => get_option( 'wc_customer_order_xml_export_suite_customers_custom_format_indent', false ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_xml_export_suite_customers_custom_format_include_all_meta' ),
			'mapping'          => get_option( 'wc_customer_order_xml_export_suite_customers_custom_format_mapping', [] ),
		] );

		wc_customer_order_csv_export()->get_formats_instance()->save_custom_format( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, $custom_customers_format );

		$custom_coupons_format = new Export_Formats\XML\Coupons_Export_Format_Definition( [
			'name'             => 'Custom - Migrated from XML Export Suite',
			'key'              => $custom_format_key,
			'indent'           => get_option( 'wc_customer_order_xml_export_suite_coupons_custom_format_indent', false ),
			'include_all_meta' => 'yes' === get_option( 'wc_customer_order_xml_export_suite_coupons_custom_format_include_all_meta' ),
			'mapping'          => get_option( 'wc_customer_order_xml_export_suite_coupons_custom_format_mapping', [] ),
		] );

		wc_customer_order_csv_export()->get_formats_instance()->save_custom_format( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS, $custom_coupons_format );
	}


	/**
	 * Migrates existing XML exports.
	 *
	 * This is triggered by the `wc_customer_order_export_migrate_xml_exports` scheduled action, registered on upgrade.
	 *
	 * @internal
	 *
	 * @since 5.0.5
	 */
	public function migrate_xml_exports() {
		global $wpdb;

		$this->get_plugin()->log( 'Migrating XML exports' );

		$job_prefix = 'wc_customer_order_xml_export_suite_background_export_job';

		$jobs = $wpdb->get_results( $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->options}
			WHERE option_name LIKE %s
		", $wpdb->esc_like( $job_prefix . '_%' ) ) );

		foreach ( $jobs as $job_row ) {

			$data = json_decode( $job_row->option_value, true );

			if ( ! is_array( $data ) || ! empty( $data['migrated'] ) ) {
				continue;
			}

			$job_id      = $data['id'];
			$export_type = $data['type'];
			$invocation  = $data['invocation'];

			// if this was an auto-export and there is now an active automation, set its ID so it can be re-transferred
			if ( 'auto' === $invocation && $automation_id = get_option( "wc_customer_order_export_migrated_active_xml_{$export_type}_automation_id" ) ) {
				$data['automation_id'] = $automation_id;
			}

			$data['output_type'] = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML;
			$data['migrated']    = true;

			$wpdb->insert( $wpdb->options, [
				'option_name'  => 'wc_customer_order_export_background_export_job_' . $job_id,
				'option_value' => json_encode( $data ),
				'autoload'     => 'no',
			] );

			// get the db items for this job
			$items = $wpdb->get_results( $wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}woocommerce_exported_xml_items
				WHERE export_id = %s
			", $job_id ) );

			foreach ( $items as $item ) {

				$wpdb->insert( $wpdb->prefix . 'woocommerce_exported_csv_items',
					[
						'export_id'      => $item->export_id,
						'content_length' => $item->content_length,
						'content'        => $item->content,
					]
				);
			}
		}
	}


}
