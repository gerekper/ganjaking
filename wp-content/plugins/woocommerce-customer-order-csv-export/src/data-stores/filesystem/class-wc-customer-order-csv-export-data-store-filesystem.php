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

/**
 * Customer/Order CSV Export Filesystem Data Store
 *
 * Persists exports to the filesystem.
 *
 * @since 4.5.0
 */
class WC_Customer_Order_CSV_Export_Data_Store_Filesystem extends WC_Customer_Order_CSV_Export_Data_Store {


	/** @var string local filesystem exports directory */
	protected $exports_dir;


	/**
	 * Sets up the filesystem data store.
	 *
	 * @since 4.5.0
	 */
	public function __construct() {

		$this->exports_dir = self::get_exports_directory();
	}


	/**
	 * Gets the directory to be used for exports.
	 *
	 * @since 4.5.0
	 *
	 * @return string exports directory
	 */
	public static function get_exports_directory() {

		$upload_dir = wp_upload_dir( null, false );

		return $upload_dir['basedir'] . '/csv_exports';
	}


	/**
	 * Persists a single item.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::store_item()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object this item is a part of
	 * @param string $content the content to store
	 */
	public function store_item( $export, $content ) {

		$path = $this->get_file_path( $export );

		if ( ! file_exists( $path ) ) {
			touch( $path );
		}

		if ( is_writable( $path ) ) {
			file_put_contents( $path, $this->prepare_content_for_storage( $content ), FILE_APPEND );
		}
	}


	/**
	 * Gets the file size of the given export in bytes.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::get_file_size()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return int file size in bytes
	 */
	public function get_file_size( $export ) {

		$path = $this->get_file_path( $export );

		return is_readable( $path ) ? filesize( $path ) : 0;
	}


	/**
	 * Deletes any stored data for the specified export.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::delete_export()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 */
	public function delete_export( $export ) {

		// suppress errors as they're not really helpful here
		@unlink( $this->get_file_path( $export ) );
	}


	/**
	 * Adds the internal filename to the job attributes before job creation.
	 *
	 * @since 4.5.0
	 *
	 * @param array $args arguments for a new job to be created from
	 * @return array additional job arguments to add
	 */
	public function get_job_args( $args ) {

		$new_args = [];

		if ( isset( $args['filename'] ) ) {
			$new_args['internal_filename'] = $this->generate_internal_filename( $args['filename'] );
		}

		return $new_args;
	}


	/**
	 * Gets the contents of an export in a single variable.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::get_output()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return string|null the output of the export or null if not found
	 */
	public function get_output( $export ) {

		$path = $this->get_file_path( $export );

		return is_readable( $path ) ? file_get_contents( $path ) : null;
	}


	/**
	 * Gets a read-only resource stream for the export file.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::get_file_stream()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return resource|false the file stream or false if unable to get file stream
	 */
	public function get_file_stream( $export ) {

		$path = $this->get_file_path( $export );

		return is_readable( $path ) ? fopen( $path, 'r' ) : false;
	}


	/**
	 * Streams data to the given file resource.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Data_Store::stream_output()
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object to stream
	 * @param resource $resource the file pointer resource to stream the export to
	 */
	public function stream_output( $export, $resource ) {

		$read_stream = $this->get_file_stream( $export );

		while ( $line = fgets( $read_stream ) ) {
			fwrite( $resource, $line );
		}

		fclose( $read_stream );
	}


	/**
	 * Gets the full path to the export file.
	 *
	 * @since 4.5.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export the export object
	 * @return string|null the export file path
	 */
	protected function get_file_path( $export ) {

		$filename = $export->get_internal_filename();

		if ( ! $filename || '' === $filename ) {

			$filename = $this->generate_internal_filename( $export->get_filename() );
			$export->set_internal_filename( $filename );
		}

		return $filename ? "{$this->exports_dir}/{$filename}" : null;
	}


	/**
	 * Generates an internal filename.
	 *
	 * @since 4.5.0
	 *
	 * @param string $filename the public-facing filename
	 * @return string internal filename
	 */
	protected function generate_internal_filename( $filename ) {

		return uniqid( null, true ) . '-' . $filename;
	}


}
