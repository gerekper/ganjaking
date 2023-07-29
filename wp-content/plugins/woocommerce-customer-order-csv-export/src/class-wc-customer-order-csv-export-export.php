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

use SkyVerge\WooCommerce\CSV_Export\CSV_Export_Generator;
use SkyVerge\WooCommerce\CSV_Export\Export_Generator;
use SkyVerge\WooCommerce\CSV_Export\XML_Export_Generator;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Export class
 *
 * Represents a single export.
 *
 * @since 4.5.0
 */
class WC_Customer_Order_CSV_Export_Export {


	/** @var stdClass object containing the job attributes */
	protected $job;

	/** @var \WC_Customer_Order_CSV_Export_Data_Store data store instance */
	protected $data_store;

	/** @var Export_Generator generator instance */
	protected $generator;


	/**
	 * Constructs an export.
	 *
	 * @since 4.5.0
	 *
	 * @param string|object|array $args {
	 *     A job ID string, a stdClass job object, or an array of arguments to create a new job with the following options:
	 *     @type string $automation_id the ID of an automation associated with the export
	 *     @type array $object_ids an array of order/customer IDs to be exported
	 *     @type string $output_type output type either `csv` or `xml`. Defaults to `csv`
	 *     @type string $type Export type either `orders` or `customers`. Defaults to `orders`
	 *     @type string $format_key an identifier for a export format definition
	 *     @type string $method Export transfer method, such as `email`, `ftp`, etc. Defaults to `download`
	 *     @type string $invocation Export invocation type, used for informational purposes. One of `manual` or `auto`, defaults to `manual`
	 *     @type string $storage_method the slug of the data store to be used for persisting the export, defaults to `database`
	 *     @type string $filename the desired export filename
	 *     @type bool $mark_as_exported whether exported objects should be marked as exported and excluded from future exports
	 *     @type bool $add_notes whether notes should be added to exported objects
	 *     @type bool $dispatch true to dispatch the background job queue after creation, false if not using background processing
	 * }
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function __construct( $args ) {

		$job      = null;
		$dispatch = false;

		// background job ID string
		if ( is_string( $args ) && '' !== $args ) {

			$job = $this->get_background_export()->get_job( $args );

		// stdClass job object
		} elseif ( is_object( $args ) ) {

			$job = $args;

		// array of attributes for a new job
		} elseif ( is_array( $args ) ) {

			$args = wp_parse_args( $args, [
				'output_type'     => WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV,
				'type'            => WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
				'method'          => 'download',
				'invocation'      => 'manual',
				'storage_method'  => 'database',
				'transfer_status' => null
			] );

			$dispatch = isset( $args['dispatch'] ) ? (bool)$args['dispatch'] : false;

			unset( $args['dispatch'] );

			$data_store = \WC_Customer_Order_CSV_Export_Data_Store_Factory::create( $args['storage_method'], $args['output_type'] );

			if ( $data_store ) {

				// allow the data store to add attributes to the job
				$args = array_merge( $args, $data_store->get_job_args( $args ) );
			}

			$job = $this->get_background_export()->create_job( $args );
		}

		if ( ! $job ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to find or create export job', 'woocommerce-customer-order-csv-export' ) );
		}

		$this->job = $job;

		if ( $dispatch ) {
			$this->get_background_export()->dispatch();
		}
	}


	/**
	 * Exports and stores an item.
	 *
	 * @since 4.5.0
	 *
	 * @param mixed $item Item to export
	 */
	public function export_item( $item ) {

		$this->store_item( $this->get_generator()->get_output( [ $item ] ) );
	}


	/**
	 * Stores a string as an export row using this export's data store.
	 *
	 * @since 4.5.0
	 *
	 * @param string $item Item to store
	 */
	public function store_item( $item ) {

		if ( ! $this->get_data_store() ) {
			return;
		}

		$this->get_data_store()->store_item( $this, $item );
	}


	/**
	 * Streams the output of this export to the given resource.
	 *
	 * @since 4.5.0
	 *
	 * @param resource $resource the file pointer resource to stream to
	 */
	public function stream_output_to_resource( $resource ) {

		if ( $this->get_data_store() ) {

			$this->get_data_store()->stream_output( $this, $resource );
		}
	}


	/**
	 * Deletes the export job along with any persisted export data.
	 *
	 * @since 4.5.0
	 */
	public function delete() {

		if ( $this->get_data_store() ) {

			$this->get_data_store()->delete_export( $this );
		}

		$this->get_background_export()->delete_job( $this->get_id() );
	}


	/**
	 * Gets the data store instance used for this export job.
	 *
	 * @since 4.5.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Data_Store|null data store instance
	 */
	public function get_data_store() {

		if ( $this->data_store ) {

			return $this->data_store;
		}

		$storage_method = $this->get_job_attr( 'storage_method' );

		if ( ! $storage_method ) {

			$storage_method = $this->determine_storage_method();

			$this->update_job_attr( 'storage_method', $storage_method );
		}

		return $storage_method ? $this->data_store = \WC_Customer_Order_CSV_Export_Data_Store_Factory::create( $storage_method, $this->get_output_type() ) : null;
	}


	/**
	 * Gets the generator instance for this export.
	 *
	 * @since 4.5.0
	 *
	 * @return Export_Generator
	 */
	public function get_generator() {

		if ( $this->generator ) {
			return $this->generator;
		}

		switch ( $this->get_output_type() ) {

			case WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV:

				require_once wc_customer_order_csv_export()->get_plugin_path() . '/src/Export_Generator.php';
				require_once wc_customer_order_csv_export()->get_plugin_path() . '/src/CSV_Export_Generator.php';
				$this->generator = new CSV_Export_Generator( $this->get_type(), $this->get_object_ids(), $this->get_format_key() );

				return $this->generator;

			break;

			case WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML:

				require_once wc_customer_order_csv_export()->get_plugin_path() . '/src/Export_Generator.php';
				require_once wc_customer_order_csv_export()->get_plugin_path() . '/src/XML_Export_Generator.php';
				$this->generator = new XML_Export_Generator( $this->get_type(), $this->get_object_ids(), $this->get_format_key() );

				return $this->generator;
			break;

			default:

				_doing_it_wrong( __FUNCTION__, sprintf( 'Invalid output type' ), '5.0.0' );
				return null;

			break;
		}
	}


	/**
	 * Gets the full contents of an export.
	 *
	 * @since 4.5.0
	 *
	 * @return string|null content of the export or null if not found
	 */
	public function get_output() {

		return $this->get_data_store() ? $this->get_data_store()->get_output( $this ) : null;
	}


	/**
	 * Gets a streamable resource for the export file.
	 *
	 * @since 4.5.0
	 *
	 * @return resource|null the file stream or null if unable to get file stream
	 */
	public function get_file_stream() {

		return $this->get_data_store() ? $this->get_data_store()->get_file_stream( $this ) : null;
	}


	/**
	 * Copies the export into a temporary file and gets the path to that file.
	 *
	 * @since 4.5.0
	 *
	 * @return string file path
	 */
	public function get_temporary_file_path() {

		$filename    = $this->get_filename();
		$file_stream = $this->get_file_stream();

		$file_path = wc_customer_order_csv_export()->get_export_handler_instance()->create_temp_file( $filename, $file_stream );

		fclose( $file_stream );

		return $file_path;
	}


	/**
	 * Updates the transfer status and saves the export.
	 *
	 * @since 4.5.0
	 *
	 * @param string $new_status the new transfer status
	 */
	public function update_transfer_status( $new_status ) {

		$this->update_job_attr( 'transfer_status', $new_status );
	}


	/**
	 * Gets the export's background job ID
	 *
	 * @since 4.5.0
	 *
	 * @return string job ID
	 */
	public function get_id() {

		return $this->get_job_attr( 'id' );
	}


	/**
	 * Gets file size of the export in bytes.
	 *
	 * @since 4.5.0
	 *
	 * @return int file size in bytes
	 */
	public function get_file_size() {

		return $this->get_data_store() ? $this->get_data_store()->get_file_size( $this ) : 0;
	}


	/**
	 * Gets the filename for this export.
	 *
	 * @since 4.5.0
	 *
	 * @return string the filename
	 */
	public function get_filename() {

		$filename = $this->get_job_attr( 'filename' );

		if ( ! $filename && $this->is_legacy_export() ) {

			$this->update_legacy_filenames();

			$filename = $this->get_job_attr( 'filename' );
		}

		return $filename;
	}


	/**
	 * Gets the object IDs being exported.
	 *
	 * @since 4.5.0
	 *
	 * @return array object IDs
	 */
	public function get_object_ids() {

		return $this->get_job_attr( 'object_ids' );
	}


	/**
	 * Gets the export transfer method.
	 *
	 * @since 4.5.0
	 *
	 * @return string export method
	 */
	public function get_transfer_method() {

		return $this->get_job_attr( 'method' );
	}


	/**
	 * Gets the output type.
	 *
	 * @since 5.0.0
	 *
	 * @return string output type - either `csv` or `xml`
	 */
	public function get_output_type() {

		$job_output_type = $this->get_job_attr( 'output_type' );

		// defaults to CSV if the job attribute is not set
		return ! empty( $job_output_type ) ? $job_output_type : WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV;
	}


	/**
	 * Gets the export type.
	 *
	 * @since 4.5.0
	 *
	 * @return string export type - either `orders` or `customers`
	 */
	public function get_type() {

		return $this->get_job_attr( 'type' );
	}


	/**
	 * Gets the export status.
	 *
	 * @since 4.5.0
	 *
	 * @return string export status
	 */
	public function get_status() {

		return $this->get_job_attr( 'status' );
	}


	/**
	 * Gets the invocation type.
	 *
	 * @since 4.5.0
	 *
	 * @return string invocation type - `manual` or `auto`
	 */
	public function get_invocation() {

		return $this->get_job_attr( 'invocation' );
	}


	/**
	 * Gets the user ID this export was created by.
	 *
	 * @since 4.5.0
	 *
	 * @return int user ID
	 */
	public function get_created_by() {

		return $this->get_job_attr( 'created_by' );
	}


	/**
	 * Gets the created-at date/time.
	 *
	 * @since 4.5.0
	 *
	 * @return string the time this export was created
	 */
	public function get_created_at() {

		return $this->get_job_attr( 'created_at' );
	}


	/**
	 * Gets the completed-at date/time.
	 *
	 * @since 4.5.0
	 *
	 * @return string the time this export was completed
	 */
	public function get_completed_at() {

		return $this->get_job_attr( 'completed_at' );
	}


	/**
	 * Gets the failed-at date/time.
	 *
	 * @since 5.0.0
	 *
	 * @return string the time this export failed
	 */
	public function get_failed_at() {

		return $this->get_job_attr( 'failed_at' );
	}


	/**
	 * Gets the export transfer status.
	 *
	 * @since 4.5.0
	 *
	 * @return string|null
	 */
	public function get_transfer_status() {

		return $this->get_job_attr( 'transfer_status' );
	}


	/**
	 * Gets the internal filename for this export, if it has one.
	 *
	 * @since 4.5.0
	 *
	 * @return string|null internal filename or null if not found
	 */
	public function get_internal_filename() {

		return $this->get_job_attr( 'internal_filename' );
	}


	/**
	 * Sets the internal filename for this export.
	 *
	 * `internal_filename` is an optional internal attribute that can be used by data stores to
	 * track an internal filename, if it differs from the public-facing `filename` attribute
	 *
	 * @since 4.5.0
	 *
	 * @param string $internal_filename the opaque filename
	 */
	public function set_internal_filename( $internal_filename ) {

		$this->update_job_attr( 'internal_filename', $internal_filename );
	}


	/**
	 * Gets the ID of the automation associated with this export.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_automation_id() {

		return $this->get_job_attr( 'automation_id' );
	}


	/**
	 * Gets the format key for the format definition that should be used in this export.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_format_key() {

		return $this->get_job_attr( 'format_key' );
	}


	/**
	 * Determines whether a note should be added to objects included in this export.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_note_enabled() {

		return (bool) $this->get_job_attr( 'add_notes' );
	}


	/**
	 * Determines whether exported objects should be marked as exported and excluded
	 * from future exports.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_mark_as_exported_enabled() {

		return (bool) $this->get_job_attr( 'mark_as_exported' );
	}


	/**
	 * Determines whether batch processing was used to start this export.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_batch_enabled() {

		return (bool) $this->get_job_attr( 'batch_enabled' );
	}


	/**
	 * Gets an attribute on the job object.
	 *
	 * @since 4.5.0
	 *
	 * @param string $attr the attribute to get
	 * @return mixed|null the attribute if it exists, null otherwise
	 */
	protected function get_job_attr( $attr ) {

		if ( $this->job && isset( $this->job->{$attr} ) ) {

			return $this->job->{$attr};
		}

		return null;
	}


	/**
	 * Updates a job attribute and saves the job to the database.
	 *
	 * @since 4.5.0
	 *
	 * @param string $attr attribute name
	 * @param mixed $value value to save
	 */
	protected function update_job_attr( $attr, $value ) {

		if ( ! $this->job ) {
			return;
		}

		$this->job->{$attr} = $value;

		// updating the job before the job has initially been persisted to the database will result in duplicate jobs
		if ( $this->get_created_at() ) {

			$this->get_background_export()->update_job( $this->job );
		}
	}


	/**
	 * Removes a job attribute and saves the job to the database.
	 *
	 * @since 4.5.0
	 *
	 * @param string $attr the attribute to remove
	 */
	protected function remove_job_attr( $attr ) {

		// don't remove the `id` attribute
		if ( 'id' === $attr ) {
			return;
		}

		unset( $this->job->{$attr} );

		$this->get_background_export()->update_job( $this->job );
	}


	/**
	 * Tries to determine the storage method for an export that doesn't have one explicitly set.
	 *
	 * This is primarily used to indicate that legacy exports use the `filesystem` storage method,
	 * but also is used to set the default storage method if, for some reason, no storage method
	 * is able to be determined on the export.
	 *
	 * @since 4.5.0
	 *
	 * @return string storage method slug
	 */
	protected function determine_storage_method() {

		// sanity check - if we're here, `storage_method` should not be set
		if ( $storage_method = $this->get_job_attr( 'storage_method' ) ) {
			return $storage_method;
		}

		if ( $this->is_legacy_export() ) {

			$this->update_legacy_filenames();

			return 'filesystem';
		}

		// when all else fails, set the default storage method to `database`
		return 'database';
	}


	/**
	 * Checks for the presence of a `file_path` attr to determine if this is a legacy export.
	 *
	 * @since 4.5.0
	 *
	 * @return bool whether this is a legacy export
	 */
	protected function is_legacy_export() {

		$file_path = $this->get_job_attr( 'file_path' );

		return is_readable( $file_path );
	}


	/**
	 * Uses the legacy `file_path` attr to update the export to the new filename format.
	 *
	 * @since 4.5.0
	 */
	protected function update_legacy_filenames() {

		$file_path = $this->get_job_attr( 'file_path' );

		// sanity check
		if ( ! $file_path || ! is_readable( $file_path ) ) {
			return;
		}

		// store the internal filename in the job
		$internal_filename = basename( $file_path );
		$this->set_internal_filename( $internal_filename );

		// store the public filename in the job
		$filename = substr( $internal_filename, strpos( $internal_filename, '-' ) + 1 );
		$this->update_job_attr( 'filename', $filename );

		// remove the legacy `file_path` attribute
		$this->remove_job_attr( 'file_path' );
	}


	/**
	 * Gets the background export handler instance, for convenience.
	 *
	 * @since 4.5.0
	 *
	 * @return \WC_Customer_Order_CSV_Export_Background_Export
	 */
	protected function get_background_export() {

		return wc_customer_order_csv_export()->get_background_export_instance();
	}


}
