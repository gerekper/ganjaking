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
 * Customer/Order XML Export Suite Handler
 *
 * Handles export actions/methods
 *
 * @since 1.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Handler {


	/** @var string $temp_filename temporary file path */
	private $temp_filename;


	/**
	 * Initialize the Export Handler
	 *
	 * In 2.0.0 Removed constructor arguments, pass arguments to dedicated methods instead
	 *
	 * @since 1.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Handler
	 */
	public function __construct() {

		add_action( 'wc_customer_order_xml_export_suite_unlink_temp_file', array( $this, 'unlink_temp_file' ) );

		add_filter( 'wc_customer_order_xml_export_suite_background_export_new_job_attrs', array( $this, 'export_header' ), 10, 2 );
	}


	/**
	 * Exports test file and uploads to remote server
	 *
	 * @since 1.1.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_upload( $export_type = 'orders' ) {

		$this->test_export_via( 'ftp', $export_type );
	}


	/**
	 * Exports test and HTTP POSTs to remote server
	 *
	 * @since 1.1.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_http_post( $export_type = 'orders' ) {

		$this->test_export_via( 'http_post', $export_type );
	}


	/**
	 * Exports test file and emails admin with the file as attachment
	 *
	 * @since 1.1.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_email( $export_type = 'orders' ) {

		$this->test_export_via( 'email', $export_type );
	}


	/**
	 * Exports a test file via the given method.
	 *
	 * @since 1.1.0
	 *
	 * @param string $method the export method
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 * @return array with 2 elements - success/error message, and message type
	 */
	public function test_export_via( $method, $export_type = 'orders' ) {

		// try to set unlimited script timeout
		@set_time_limit( 0 );

		try {

			// get method (download, FTP, etc)
			$export = wc_customer_order_xml_export_suite()->get_methods_instance()->get_export_method( $method, $export_type );

			if ( ! is_object( $export ) ) {

				/** translators: %s - export method identifier */
				throw new SV_WC_Plugin_Exception( sprintf( __( 'Invalid Export Method: %s', 'woocommerce-customer-order-xml-export-suite' ), $method ) );
			}

			// create a temp file with the test data
			$temp_file = $this->create_temp_file( $this->get_test_filename(), $this->get_test_data() );

			// simple test file
			if ( $export->perform_action( $temp_file ) ) {
				return array( __( 'Test was successful!', 'woocommerce-customer-order-xml-export-suite' ), 'success' );
			} else {
				return array( __( 'Test failed!', 'woocommerce-customer-order-xml-export-suite' ), 'error' );
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			// log errors
			wc_customer_order_xml_export_suite()->log( $e->getMessage() );

			/** translators: %s - error message */
			return array( sprintf( __( 'Test failed: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ), 'error' );
		}
	}


	/**
	 * Creates a temp file that is automatically removed on shutdown or after a given delay.
	 *
	 * @since 2.0.0
	 *
	 * @param string $filename the filename
	 * @param string|resource $source path to source file, data to write to the file, or a resource containing the data
	 * @param string $temp_path path to dir to place the temp file in. Defaults to the WP temp dir.
	 * @param bool|int $delay_remove Delay temp file removal by amount of seconds.
	 *                               If boolean true, this will default to 60 seconds.
	 *                               Normally, temp files are removed once the script
	 *                               exits, but with this param set to true, it's possible
	 *                               to keep it for a short while. This helps
	 *                               in cases when the file has to be accessible briefly
	 *                               after the script exists (such as for redirect downloads ).
	 * @return string $filename path to the temp file
	 */
	public function create_temp_file( $filename, $source, $temp_path = '', $delay_remove = false ) {

		$temp_path = $temp_path && is_readable( $temp_path ) ? $temp_path : get_temp_dir();

		// prepend the temp directory to filename
		$filename = untrailingslashit( $temp_path ) . '/' . $filename;

		// source is a resource that contains the data
		if ( is_resource( $source ) ) {

			touch( $filename );

			$temp_file = @fopen( $filename, 'w+' );

			while ( $data = fread( $source, 1024 ) ) {
				fwrite( $temp_file, $data );
			}

			fclose( $temp_file );

			// source is a path to existing file
		} elseif ( is_string( $source ) && is_readable( $source ) ) {

			copy( $source, $filename  );

			// if not a readable source file, it's most likely just raw data
		} else {

			// create the file
			touch( $filename );

			// open the file, write file, and close it
			$fp = @fopen( $filename, 'w+');

			@fwrite( $fp, $source );
			@fclose( $fp );
		}

		// make sure the temp file is removed afterwards

		// delay the remove for the given period
		if ( $delay_remove ) {

			if ( ! is_int( $delay_remove ) ) {
				$delay_remove = 60; // default to 60 seconds
			}

			wp_schedule_single_event( time() + $delay_remove, 'wc_customer_order_xml_export_suite_unlink_temp_file', array( $filename ) );
		}

		// ...or simply remove on shutdown
		else {
			$this->temp_filename = $filename;
			register_shutdown_function( array( $this, 'unlink_temp_file' ) );
		}

		return $filename;
	}


	/**
	 * Unlink temp file
	 *
	 * @since 2.0.0
	 * @param string $file_path Optional. If not provided, will look for file path
	 *                          on $this->temp_filename;
	 */
	public function unlink_temp_file( $file_path = '' ) {

		if ( ! $file_path ) {
			$file_path = $this->temp_filename;
		}

		if ( $file_path ) {
			@unlink( $file_path );
		}
	}


	/**
	 * Marks orders as exported by setting the `_wc_customer_order_xml_export_suite_is_exported` order meta flag
	 *
	 * In 2.0.0 added $ids param as the first param
	 *
	 * @since 1.1.0
	 * @param array $ids
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 */
	public function mark_orders_as_exported( $ids, $method = 'download' ) {

		if ( empty( $ids ) ) {
			return;
		}

		foreach ( $ids as $order_id ) {

			$order      = wc_get_order( $order_id );
			$order_note = null;

			// only add order notes if the option is turned on and order has not already been exported
			$add_order_note = ( 'yes' === get_option( 'wc_customer_order_xml_export_suite_orders_add_note' ) ) && ! get_post_meta( $order_id, '_wc_customer_order_xml_export_suite_is_exported', true );

			/**
			 * Filter if an order note should be added when an order is successfully exported
			 *
			 * @since 2.0.0
			 * @param bool $add_order_note true if the order note should be added, false otherwise
			 */
			if ( apply_filters( 'wc_customer_order_xml_export_suite_add_order_note', $add_order_note ) ) {

				switch ( $method ) {

					// note that order downloads using the AJAX order action are not marked or noted, only bulk order downloads
					case 'download':
						$order_note = esc_html__( 'Order exported to XML and successfully downloaded.', 'woocommerce-customer-order-xml-export-suite' );
					break;

					case 'ftp':
						$order_note = esc_html__( 'Order exported to XML and successfully uploaded to server.', 'woocommerce-customer-order-xml-export-suite' );
					break;

					case 'http_post':
						$order_note = esc_html__( 'Order exported to XML and successfully POSTed to remote server.', 'woocommerce-customer-order-xml-export-suite' );
					break;

					case 'email':
						$order_note = esc_html__( 'Order exported to XML and successfully emailed.', 'woocommerce-customer-order-xml-export-suite' );
					break;

					default:
						$order_note = esc_html__( 'Order exported to XML.', 'woocommerce-customer-order-xml-export-suite' );
					break;
				}

				$order->add_order_note( $order_note );
			}

			/**
			 * Filters whether to add the "exported" flag to orders or not.
			 *
			 * TODO: move to a compat / WC 3.0+ method here when dropping WC 2.6 support {BR 2017-05-04}
			 *
			 * @since 2.2.0
			 *
			 * @param bool $mark_as_exported whether to mark the order as exported; defaults to true
			 * @param \WC_Order $order order being exported
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param \WC_Customer_Order_XML_Export_Suite_Handler $this, handler instance
			 */
			if ( apply_filters( 'wc_customer_order_xml_export_suite_mark_order_exported', true, $order, $method, $this ) ) {
				update_post_meta( $order_id, '_wc_customer_order_xml_export_suite_is_exported', 1 );
			}

			/**
			 * Order Exported Action.
			 *
			 * Fired when an order is exported.
			 *
			 * @since 2.0.0
			 * @param WC_Order $order order being exported
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param string|null $order_note order note message, null if no note was added
			 * @param \WC_Customer_Order_XML_Export_Suite_Handler $this, handler instance
			 */
			do_action( 'wc_customer_order_xml_export_suite_order_exported', $order, $method, $order_note, $this );
		}
	}


	/**
	 * Marks customers as exported by setting the `_wc_customer_order_xml_export_suite_is_exported`
	 * user meta flag on users, and `_wc_customer_order_xml_export_suite_customer_is_exported` on
	 * orders for guest customers.
	 *
	 * @since 2.0.0
	 * @param array $ids customer IDs to to mark as exported. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: array( $user_id, array( $billing_email, $order_id ) )
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 */
	public function mark_customers_as_exported( $ids, $method = 'download' ) {

		foreach ( $ids as $customer_id ) {

			$order_id = $email = $user = null;

			if ( is_array( $customer_id ) ) {

				list( $email, $order_id ) = $customer_id;

				/**
				 * Filters whether to add the "exported" flag to guest customers or not.
				 *
				 * TODO: move to a compat / WC 3.0+ method here when dropping WC 2.6 support {IT 2017-05-04}
				 *
				 * @since 2.2.0
				 *
				 * @param bool $mark_as_exported whether to mark the guest as exported; defaults to true
				 * @param int $user_id ID of the customer being exported, 0 for guests
				 * @param string $method how the customer is exported (ftp, download, etc)
				 * @param \WC_Customer_Order_XML_Export_Suite_Handler $this, handler instance
				 */
				if ( apply_filters( 'wc_customer_order_xml_export_suite_mark_customer_exported', true, 0, $method, $this ) ) {
					update_post_meta( $order_id, '_wc_customer_order_xml_export_suite_customer_is_exported', 1 );
				}

			} else {

				$user = is_numeric( $customer_id ) ? get_user_by( 'id', $customer_id ) : get_user_by( 'email', $customer_id );

				if ( $user ) {

					$email = $user->user_email;

					/**
					 * Filters whether to add the "exported" flag to registered customers or not.
					 *
					 * @since 2.2.0
					 *
					 * @param bool $mark_as_exported whether to mark the user as exported; defaults to true
					 * @param int $user_id ID of the customer being exported, 0 for guests
					 * @param string $method how the customer is exported (ftp, download, etc)
					 * @param \WC_Customer_Order_XML_Export_Suite_Handler $this, handler instance
					 */
					if ( apply_filters( 'wc_customer_order_xml_export_suite_mark_customer_exported', true, $user->ID, $method, $this ) ) {
						update_user_meta( $user->ID, '_wc_customer_order_xml_export_suite_is_exported', 1 );
					}

				}
			}

			/**
			 * Customer Exported Action.
			 *
			 * Fired when a customer is exported.
			 *
			 * @since 2.0.0
			 * @param string $email customer email being exported
			 * @param int|null $user_id customer user ID being exported, null if guest customer
			 * @param int|null $order_id related order ID, used for guest customers, may be null if no related order
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param \WC_Customer_Order_XML_Export_Suite_Handler $this, handler instance
			 */
			do_action( 'wc_customer_order_xml_export_suite_customer_exported', $email, ( $user ? $user->ID : null ), $order_id, $method, $this );
		}
	}


	/**
	 * Replaces variables in file name setting (e.g. %%timestamp%% becomes 2013_03_20_16_22_14 )
	 *
	 * In 2.0.0 added $ids and $export_type params
	 *
	 * @since 1.0.0
	 * @param array $ids
	 * @param string $export_type
	 * @return string filename with variables replaced
	 */
	protected function replace_filename_variables( $ids, $export_type ) {

		$pre_replace_filename = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_filename' );

		$variables = array(
			'%%timestamp%%' => date( 'Y_m_d_H_i_s', current_time( 'timestamp' ) ),
		);

		if ( 'orders' === $export_type ) {
			$variables['%%order_ids%%'] = implode( '-', $ids );
		}

		/**
		 * Allow actors to adjust filename merge vars and their replacements
		 *
		 * @since 2.0.0
		 * @param array $variables associative array of variables and their replacement values
		 * @param array $ids
		 * @param string $export_type
		 */
		$variables = apply_filters( 'wc_customer_order_xml_export_suite_filename_variables', $variables, $ids, $export_type );

		$post_replace_filename = ! empty( $variables ) ? str_replace( array_keys( $variables ), array_values( $variables ), $pre_replace_filename ) : $pre_replace_filename;

		/**
		 * Filter exported file name
		 *
		 * In 2.0.0 renamed from `wc_customer_order_xml_export_suite_export_file_name`
		 * to `wc_customer_order_xml_export_suite_filename`
		 *
		 * @since 1.0.0
		 * @param string $filename Filename after replacing variables
		 * @param string $pre_replace_filename Filename before replacing variables
		 * @param array $ids Array of entity (customer or order) IDs being exported
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_filename', $post_replace_filename, $pre_replace_filename, $ids );
	}


	/**
	 * Get background export handler instance
	 *
	 * Shortcut method for convenience
	 *
	 * @since 2.0.0
	 * @return \WC_Customer_Order_XML_Export_Suite_Background_Export instance
	 */
	private function get_background_export_handler() {

		return wc_customer_order_xml_export_suite()->get_background_export_instance();
	}


	/**
	 * Gets an export by its ID.
	 *
	 * A simple wrapper around SV_WP_Background_Job_Handler::get_job(), for convenience
	 *
	 * @see SV_WP_Background_Job_Handler::get_job()
	 *
	 * @since 2.0.0
	 * @deprecated since 2.4.0
	 *
	 * @param string $id
	 * @return object|null
	 */
	public function get_export( $id ) {

		_doing_it_wrong( 'WC_Customer_Order_XML_Export_Suite_Handler::get_export()', __( 'This method has been deprecated. Use wc_customer_order_xml_export_suite_get_export() instead.', 'woocommerce-customer-order-xml-export-suite' ), '2.4.0' );

		return $this->get_background_export_handler()->get_job( $id );
	}


	/**
	 * Get an array of exports
	 *
	 * A simple wrapper around SV_WP_Background_Job_Handler::get_jobs(), for convenience
	 *
	 * @since 2.0.0
	 * @see SV_WP_Background_Job_Handler::get_jobs()
	 * @param array $args Optional. An array of arguments passed to SV_WP_Background_Job_Handler::get_jobs()
	 * @return array Found export objects
	 */
	public function get_exports( $args = array() ) {

		return wc_customer_order_xml_export_suite()->get_background_export_instance()->get_jobs( $args );
	}


	/**
	 * Transfers an export via the given method (FTP, etc).
	 *
	 * @since 2.0.0
	 *
	 * @param string|object $export_id Export (job) instance or ID
	 * @param string $export_method Export method, will default to export's own if not provided
	 * @throws \SV_WC_Plugin_Exception
	 */
	public function transfer_export( $export_id, $export_method = null ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export_id );

		if ( ! $export ) {
			/* translators: Placeholders: %s - export ID */
			throw new SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not find export: %s', 'woocommerce-customer-order-xml-export-suite' ), $export_id ) );
		}

		$export_method = $export_method ? $export_method : $export->get_transfer_method();
		$_export_method = wc_customer_order_xml_export_suite()->get_methods_instance()->get_export_method( $export_method, $export->get_type(), $export->get_completed_at() );

		if ( ! is_object( $_export_method ) ) {
			/* translators: Placeholders: %s - export method */
			throw new SV_WC_Plugin_Exception( sprintf( esc_html__( 'Invalid Export Method: %s', 'woocommerce-customer-order-xml-export-suite' ), $export_method ) );
		}

		$export->update_transfer_status( 'processing' );

		// perform the transfer action
		try {

			$_export_method->perform_action( $export );

			$export->update_transfer_status( 'completed' );

			// Mark orders/customers as exported
			if ( 'orders' === $export->get_type() ) {

				$this->mark_orders_as_exported( $export->get_object_ids(), $export->get_transfer_method() );
			} elseif ( 'customers' === $export->get_type() ) {

				$this->mark_customers_as_exported( $export->get_object_ids(), $export->get_transfer_method() );
			}

		} catch ( Exception $e ) {

			wc_customer_order_xml_export_suite()->log( sprintf( esc_html__( 'Error performing export: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );

			$export->update_transfer_status( 'failed' );

			throw $e;
		}
	}


	/**
	 * Return the export generator class instance
	 *
	 * @since 2.0.0
	 * @param string $export_type Export type
	 * @return \WC_Customer_Order_XML_Export_Suite_Generator
	 */
	protected function get_generator( $export_type ) {

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-generator.php' );

		return new WC_Customer_Order_XML_Export_Suite_Generator( $export_type );
	}


	/**
	 * Return the filename for test export
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_test_filename() {
		return 'test.xml';
	}


	/**
	 * Return the data (file contents) for test export
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_test_data() {
		return '<?xml version="1.0"?><test></test>';
	}


	/**
	 * Gets exports directory path in local filesystem.
	 *
	 * @since 2.0.0
	 * @deprecated since 2.4.0
	 *
	 * @return string
	 */
	public function get_exports_dir() {

		_doing_it_wrong( 'WC_Customer_Order_XML_Export_Suite_Handler::get_exports_dir()', __( 'This method has been deprecated.' ), '2.4.0' );

		return '';
	}


	/**
	 * Get exports directory URL for downloads
	 *
	 * @since 2.0.0
	 * @deprecated since 2.4.0
	 *
	 * @return string
	 */
	public function get_exports_url() {

		_doing_it_wrong( 'WC_Customer_Order_XML_Export_Suite_Handler::get_exports_dir()', __( 'This method has been deprecated.' ), '2.4.0' );

		return '';
	}


	/**
	 * Kicks off an export.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string|array $ids
	 * @param array $args {
	 *                 An array of arguments
	 *                 @type string $type Export type either `orders` or `customers`. Defaults to `orders`
	 *                 @type string $method Export transfer method, such as `email`, `ftp`, etc. Defaults to `download`
	 *                 @type string $invocation Export invocation type, used for informational purposes. One of `manual` or `auto`, defaults to `manual`
	 * }
	 * @return \WC_Customer_Order_XML_Export_Suite_Export|false export object or false on failure
	 * @throws \SV_WC_Plugin_Exception
	 */
	public function start_export( $ids, $args = array() ) {

		// make sure default args are set
		$args = wp_parse_args( $args, array(
			'type'       => 'orders',
			'method'     => 'download',
			'invocation' => 'manual',
		) );

		// handle single order/customer exports
		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		/**
		 * Allow actors to change the order/customer IDs to be exported
		 *
		 * In 2.0.0 renamed from `wc_customer_order_xml_export_suite_order_ids` to
		 * `wc_customer_order_xml_export_suite_ids`, removed $this param, added $args
		 * param, moved here from WC_Customer_Order_XML_Export_Suite_Writer class
		 *
		 * @since 1.8.1
		 * @param array $ids Order/customer IDs to be exported.
		 * @param array $args Export args, see WC_Customer_Order_XML_Export_Suite_Handler::start_export()
		 */
		$ids = apply_filters( 'wc_customer_order_xml_export_suite_ids', $ids, $args );

		// no need to export if we have no ids
		if ( empty( $ids ) ) {
			return false;
		}

		$export_args = array(
			'object_ids'      => $ids,
			'invocation'      => $args['invocation'],
			'type'            => $args['type'],
			'method'          => $args['method'],
			'transfer_status' => null,
			'storage_method'  => 'database',
			'filename'        => $this->replace_filename_variables( $ids, $args['type'] ),
			'dispatch'        => ! wc_customer_order_xml_export_suite()->is_batch_processing_enabled(),
		);

		/**
		 * Filters all the export arguments just before starting a new export.
		 *
		 * @see \WC_Customer_Order_XML_Export_Suite_Export::__construct() for a full list of argument options
		 *
		 * @since 2.4.0
		 *
		 * @param array $export_args the arguments being passed in to this export
		 */
		$export_args = apply_filters( 'wc_customer_order_xml_export_suite_start_export_args', $export_args );

		return new WC_Customer_Order_XML_Export_Suite_Export( $export_args );
	}


	/**
	 * Exports a single item.
	 *
	 * This method will normally only ever be called from the background export to
	 * export items one-by-one.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $item Item to export
	 * @param object $job_obj Export (job) associated with the item
	 */
	public function export_item( $item, $job_obj) {

		if ( $export = wc_customer_order_xml_export_suite_get_export( $job_obj ) ) {

			$export->export_item( $item );
		}
	}


	/**
	 * Stores the export header before the job is enqueued.
	 *
	 * This is necessary to prevent a race condition, since the job ID is needed to persist any part of the
	 * export, but we can't get the ID before it is enqueued except through this filter, and we need to make
	 * sure that the header is the first item within the exported data.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 *
	 * @param array $job_attrs job attributes
	 * @param string $job_id the job ID
	 * @return array job attributes
	 */
	public function export_header( $job_attrs, $job_id ) {

		if ( isset( $job_attrs['type'], $job_attrs['object_ids'], $job_attrs['storage_method'] ) ) {

			// throw in the job ID and emulate a stdClass job object
			$job    = (object) array_merge( $job_attrs, array( 'id' => $job_id ) );
			$export = wc_customer_order_xml_export_suite_get_export( $job );

			if ( $export ) {

				$header = $export->get_generator()->get_header();

				if ( $header && '' !== $header ) {

					$export->store_item( $header );
				}
			}
		}

		return $job_attrs;
	}


	/**
	 * Finishes off an export.
	 *
	 * @since 2.0.0
	 *
	 * @param object|string $export Export object or ID
	 */
	public function finish_export( $export ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export ) {
			return;
		}

		if ( 'auto' === $export->get_invocation() ) {

			/**
			 * Auto-Export Action.
			 *
			 * Fired when orders or customers are auto-exported.
			 * Moved from WC_Customer_Order_XML_Export_Suite_Cron class in 2.0.0.
			 *
			 * @since 1.0.0
			 * @param array $order_ids order IDs that were exported
			 */
			do_action( 'wc_customer_order_xml_export_suite_' . $export->get_type() . '_exported', $export->get_object_ids() );
		}

		$footer = $this->get_generator( $export->get_type() )->get_footer();

		$export->store_item( $footer );

		if ( in_array( $export->get_transfer_method(), array( 'download', 'local' ), true ) ) {

			// Mark orders/customers as exported
			if ( 'orders' === $export->get_type() ) {

				$this->mark_orders_as_exported( $export->get_object_ids(), $export->get_transfer_method() );

			} elseif ( 'customers' === $export->get_type() ) {

				$this->mark_customers_as_exported( $export->get_object_ids(), $export->get_transfer_method() );

			}

		} else {

			try {

				// transfer file via the provided export method
				$this->transfer_export( $export );

			} catch ( SV_WC_Plugin_Exception $e ) {

				// log errors
				/* translators: Placeholders: %s - error message */
				wc_customer_order_xml_export_suite()->log( sprintf( __( 'Failed to transfer exported file: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );
			}
		}

		// Notify the user that the export is complete
		$this->add_export_finished_notice( $export );
	}


	/**
	 * Handles a failed export.
	 *
	 * @since 2.0.0
	 *
	 * @param object|string $export Export job object or ID
	 */
	public function failed_export( $export ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export ) {
			return;
		}

		$this->add_export_finished_notice( $export );
	}


	/**
	 * Adds export finished notice for a user.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export|object|string $export Export object or ID
	 */
	public function add_export_finished_notice( $export ) {

		if ( ! $export instanceof WC_Customer_Order_XML_Export_Suite_Export ) {
			$export = wc_customer_order_xml_export_suite_get_export( $export );
		}

		// don't notify if no export found
		if ( ! $export ) {
			return;
		}

		// Notify the user that the manual export failed
		if ( 'manual' === $export->get_invocation() ) {

			$message_id = 'wc_customer_order_xml_export_suite_finished_' . $export->get_id();

			// add notice for manually created exports
			if ( $export->get_created_by() && ! wc_customer_order_xml_export_suite()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $export->get_created_by() ) ) {

				$export_notices = get_user_meta( $export->get_created_by(), '_wc_customer_order_xml_export_suite_notices', true );

				if ( ! $export_notices ) {
					$export_notices = array();
				}

				$export_notices[] = $export->get_id();

				update_user_meta( $export->get_created_by(), '_wc_customer_order_xml_export_suite_notices', $export_notices );
			}

		}

		$successful = 'completed' === $export->get_status() && ( ! $export->get_transfer_status() || 'completed' === $export->get_transfer_status() );

		// Notify admins that automatic exports are failing
		if ( ! $successful && 'auto' === $export->get_invocation() ) {

			$failure_type      = 'failed' === $export->get_status() ? 'export' : 'transfer';
			$failure_notices   = get_option( 'wc_customer_order_xml_export_suite_failure_notices', array() );
			$multiple_failures = ! empty( $failure_notices ) && ! empty( $failure_notices[ $failure_type ] );

			$failure_notices[ $failure_type ] = array(
				'export_id'         => $export->get_id(),
				'multiple_failures' => $multiple_failures,
			);

			update_option( 'wc_customer_order_xml_export_suite_failure_notices', $failure_notices );
		}

	}


	/**
	 * Removes export finished notice from user meta.
	 *
	 * @since 2.0.0
	 *
	 * @param object|string $export Export object or ID
	 * @param int $user_id
	 */
	public function remove_export_finished_notice( $export, $user_id ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export || ! $user_id ) {
			return;
		}

		$export_id = $export->get_id();

		$export_notices = get_user_meta( $user_id, '_wc_customer_order_xml_export_suite_notices', true );

		if ( ! empty( $export_notices ) && in_array( $export_id, $export_notices, true ) ) {

			unset( $export_notices[ array_search( $export_id, $export_notices ) ] );

			update_user_meta( $user_id, '_wc_customer_order_xml_export_suite_notices', $export_notices );
		}

		// also remove the message from user dismissed notices
		$dismissed_notices = wc_customer_order_xml_export_suite()->get_admin_notice_handler()->get_dismissed_notices( $user_id );
		$message_id        = 'wc_customer_order_xml_export_suite_finished_' . $export_id;

		if ( ! empty( $dismissed_notices ) && isset( $dismissed_notices[ $message_id ] ) ) {
			unset( $dismissed_notices[ $message_id ] );

			update_user_meta( $user_id, '_wc_plugin_framework_customer_order_xml_export_suite_dismissed_messages', $dismissed_notices );
		}
	}


	/**
	 * Removes expired exports.
	 *
	 * Deletes completed/failed exports older than the maximum age (14 days by default)
	 *
	 * @since 2.0.0
	 */
	public function remove_expired_exports() {

		// get all completed or failed jobs
		$all_jobs = $this->get_background_export_handler()->get_jobs( array( 'completed', 'failed' ) );

		if ( empty( $all_jobs ) ) {
			return;
		}

		// loop over the jobs and find those that should be removed
		foreach ( $all_jobs as $job ) {

			$date = 'completed' === $job->status ? $job->completed_at : $job->failed_at;

			// job completed/failed within the max age timeframe, remove it (along with the file)
			if ( strtotime( $date ) <= current_time( 'timestamp' ) - $this->get_export_max_age() ) {

				$export = wc_customer_order_xml_export_suite_get_export( $job );

				if ( $export ) {

					$export->delete();
				}
			}
		}
	}


	/**
	 * Gets the maximum age of stored exports, in seconds.
	 *
	 * @since 2.5.1
	 *
	 * @return int
	 */
	public function get_export_max_age() {

		/**
		 * Filters the maximum age of stored exports.
		 *
		 * @since 2.5.1
		 *
		 * @param int $max_age the maximum age of stored exports, in seconds
		 */
		return (int) apply_filters( 'wc_customer_order_xml_export_suite_export_max_age', 14 * DAY_IN_SECONDS );
	}


}
