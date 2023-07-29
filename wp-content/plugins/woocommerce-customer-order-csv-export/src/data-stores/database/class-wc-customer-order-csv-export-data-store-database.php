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

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Database Data Store
 *
 * Persists exports to the database.
 *
 * @since 4.5.0
 */
class WC_Customer_Order_CSV_Export_Data_Store_Database extends WC_Customer_Order_CSV_Export_Data_Store {


	/** @var string custom database stream wrapper protocol */
	const STREAM_WRAPPER_PROTOCOL = 'wc-csv-export-database';


	/** @var string name of the database table used to store exports */
	protected $table_name;


	/**
	 * Constructs the data store.
	 *
	 * @since 4.5.0
	 */
	public function __construct() {

		$this->table_name = self::get_table_name();

		require_once wc_customer_order_csv_export()->get_plugin_path() . '/src/class-wc-customer-order-csv-export-lifecycle.php';

		// create the database table if it doesn't exist
		if ( ! WC_Customer_Order_CSV_Export_Lifecycle::validate_table() ) {
			WC_Customer_Order_CSV_Export_Lifecycle::create_tables();
		}

		// register the database stream wrapper if it isn't already registered
		if ( ! in_array( self::STREAM_WRAPPER_PROTOCOL, stream_get_wrappers(), true ) ) {

			stream_wrapper_register( self::STREAM_WRAPPER_PROTOCOL, 'WC_Customer_Order_CSV_Export_Database_Stream_Wrapper' );
		}
	}


	/**
	 * Gets the table name used for storing exports.
	 *
	 * @since 4.5.0
	 *
	 * @return string table name
	 */
	public static function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'woocommerce_exported_csv_items';
	}


	/**
	 * Returns the database schema needed to create the table for this data store.
	 *
	 * @since 4.5.0
	 *
	 * @return string database schema
	 */
	public static function get_table_schema() {
		global $wpdb;

		$table_name = self::get_table_name();
		$collate    = $collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		return "

CREATE TABLE {$table_name} (
  id BIGINT(20) unsigned NOT NULL auto_increment,
  export_id VARCHAR(32) NOT NULL default '',
  content_length INT UNSIGNED NOT NULL default 0,
  content LONGTEXT NOT NULL default '',
  PRIMARY KEY (id ASC),
  KEY export_id (export_id ASC)
) $collate;
		";
	}


	/**
	 * Stores an item in the database.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::store_item()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object this item is a part of
	 * @param string $content the content to be stored
	 * @return bool whether or not this item was stored successfully
	 */
	public function store_item( $export, $content ) {
		global $wpdb;

		// sanity checks to prevent empty data from being written
		if ( ! $export || ! is_string( $content ) || '' === trim( $content ) || ! $export->get_id() ) {
			return false;
		}

		// `content_length` is tracked so we can re-assemble the overall file size for download headers, so this
		// should track with the content as it would exist in a downloaded export, not as it will be persisted in the db.
		// Consequently, `content_length` and the actual length of the data inside `content` will not be equivalent.
		$result = $wpdb->insert( $this->table_name, [
			'export_id'      => $export->get_id(),
			'content_length' => mb_strlen( $content, '8bit' ),
			'content'        => $this->prepare_content_for_storage( $content )
		] );

		return (bool) $result;
	}


	/**
	 * Gets the file size of the given export.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::get_file_size()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return int file size in bytes
	 */
	public function get_file_size( $export ) {
		global $wpdb;

		$file_size = $wpdb->get_var( $wpdb->prepare( 'SELECT SUM( content_length ) as file_size FROM ' . $this->table_name . ' WHERE export_id = %s', $export->get_id() ) );

		return $file_size ? $file_size : 0;
	}


	/**
	 * Streams the contents of the export into a variable and returns it.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::get_output()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return string|null the contents of the export
	 */
	public function get_output( $export ) {

		$stream = $this->get_file_stream( $export );

		if ( $stream ) {
			return stream_get_contents( $stream );
		}

		return null;
	}


	/**
	 * Streams an export to the given resource.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::stream_output()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object to stream
	 * @param resource $resource the file pointer resource to stream data to
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function stream_output( $export, $resource ) {
		global $wpdb;

		// mysqli is required for streaming, but we can fallback and still deliver the export data inefficiently
		if ( ! $wpdb->dbh instanceof mysqli ) {

			$this->inefficient_stream_output( $export, $resource );

			return;
		}

		$export_iterator = new WC_Customer_Order_CSV_Export_Database_Stream_Iterator( $export );

		if ( $export_iterator ) {

			foreach( $export_iterator as $line ) {

				fwrite( $resource, $line );
			}
		}
	}


	/**
	 * Streams an export to the given resource inefficiently.
	 *
	 * This is a fallback method in case mysqli is not enabled on the host, or wasn't used
	 * by WordPress to connect to the database, for some reason. This should never be called on
	 * a system where mysqli is installed and configured correctly.
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object to stream
	 * @param resource $resource the file pointer resource to stream data to
	 * @throws Framework\SV_WC_Plugin_Exception if called in an inappropriate server context
	 */
	private function inefficient_stream_output( $export, $resource ) {
		global $wpdb;

		if ( ! is_resource( $wpdb->dbh ) || ! function_exists( 'mysql_query' ) || ! 'mysql link' === get_resource_type( $wpdb->dbh ) ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to locate a legacy mysql connection in this WordPress install', 'woocommerce-customer-order-csv-export' ) );
		}

		$db_result = mysql_query( $wpdb->prepare( 'SELECT id, content FROM ' . $this->table_name . ' WHERE export_id = %s ORDER BY id ASC', $export->get_id() ), $wpdb->dbh );

		while ( $row = mysql_fetch_assoc( $db_result ) ) {

			$line = $this->parse_row( $row );

			fwrite( $resource, $line );
		}
	}


	/**
	 * Gets a streamable resource for the export file.
	 *
	 * Always remember to close the stream with fclose() when finished.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::get_file_stream()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return resource|false the file stream or false if unable to get file stream
	 */
	public function get_file_stream( $export ) {

		return fopen( self::STREAM_WRAPPER_PROTOCOL . "://{$export->get_id()}", 'r' );
	}


	/**
	 * Gets the data stream for a given export.
	 *
	 * Be sure to free the result using mysqli_free_result() when finished.
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return mysqli_result|null the mysqli result or null if unable to get the result
	 */
	public function get_data_stream( $export ) {
		global $wpdb;

		$db_result = mysqli_query( $wpdb->dbh, $wpdb->prepare( 'SELECT id, content FROM ' . $this->table_name . ' WHERE export_id = %s ORDER BY id ASC', $export->get_id() ), MYSQLI_USE_RESULT );

		return $db_result ? $db_result : null;
	}


	/**
	 * Deletes any stored data for the specified export.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::delete_export()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 */
	public function delete_export( $export ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $this->table_name . ' WHERE export_id = %s', $export->get_id() ) );
	}


	/**
	 * Parses a row result from the database.
	 *
	 * @since 4.5.0
	 *
	 * @param string[] $row row from the database query fetch_assoc()
	 * @return string parsed data
	 */
	public function parse_row( $row ) {

		if ( $row && isset( $row['content'] ) ) {

			return $this->process_content_from_storage( $row['content'] );
		}

		return false;
	}


	/**
	 * Prepares content for storage.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::prepare_content_for_storage()
	 *
	 * @since 4.5.0
	 *
	 * @param string $content the content being stored
	 * @return string the JSON-encoded content prepared for storage
	 */
	protected function prepare_content_for_storage( $content ) {

		return json_encode( $content );
	}


	/**
	 * Processes content from storage.
	 *
	 * @see WC_Customer_Order_CSV_Export_Data_Store::process_content_from_storage()
	 *
	 * @since 4.5.0
	 *
	 * @param string $content the content being fetched from storage
	 * @return string the JSON-decoded content
	 */
	protected function process_content_from_storage( $content ) {

		return json_decode( $content );
	}


}
