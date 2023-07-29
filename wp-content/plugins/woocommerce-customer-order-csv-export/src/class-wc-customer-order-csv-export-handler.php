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

use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;
use SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Handler
 *
 * Handles export actions/methods
 *
 * @since 3.0.0
 */
class WC_Customer_Order_CSV_Export_Handler {


	/** @var string $temp_filename temporary file path */
	protected $temp_filename;


	/**
	 * Initialize the Export Handler
	 *
	 * In 4.0.0 Removed constructor arguments, pass arguments to dedicated methods instead
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		add_action( 'wc_customer_order_export_unlink_temp_file', [ $this, 'unlink_temp_file' ] );

		add_filter( 'wc_customer_order_export_background_export_new_job_attrs', [ $this, 'export_header' ], 10, 2 );
	}


	/**
	 * Exports test file and uploads to remote server
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_upload( $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ) {

		$this->test_export_via( 'ftp', $export_type );
	}


	/**
	 * Exports test and HTTP POSTs to remote server
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_http_post( $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ) {

		$this->test_export_via( 'http_post', $export_type );
	}


	/**
	 * Exports test file and emails admin with the file as attachment
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_email( $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ) {

		$this->test_export_via( 'email', $export_type );
	}


	/**
	 * Exports a test file via the given method.
	 *
	 * @since 3.0.0
	 *
	 * @param string $method the export method
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 * @param string $output_type Optional, the output type. defaults to `csv`
	 * @param array $method_args transfer method args
	 * @return array with 2 elements - success/error message, and message type
	 */
	public function test_export_via( $method, $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $method_args = [] ) {

		// try to set unlimited script timeout
		@set_time_limit( 0 );

		try {

			// get method (download, FTP, etc)
			$export = wc_customer_order_csv_export()->get_methods_instance()->get_export_method( $method, $export_type, '', $output_type, $method_args );

			if ( ! $export instanceof WC_Customer_Order_CSV_Export_Method ) {

				/** translators: %s - export method identifier */
				throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Invalid Export Method: %s', 'woocommerce-customer-order-csv-export' ), $method ) );
			}

			// create a temp file with the test data
			$temp_file = $this->create_temp_file( $this->get_test_filename( $output_type ), $this->get_test_data( $output_type ) );

			// simple test file
			if ( $export->perform_action( $temp_file ) ) {
				return [ __( 'Test was successful!', 'woocommerce-customer-order-csv-export' ), 'success' ];
			}

			return [ __( 'Test failed!', 'woocommerce-customer-order-csv-export' ), 'error' ];

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			// log errors
			wc_customer_order_csv_export()->log( $e->getMessage() );

			/** translators: %s - error message */
			return [ sprintf( __( 'Test failed: %s', 'woocommerce-customer-order-csv-export' ), $e->getMessage() ), 'error' ];
		}
	}


	/**
	 * Creates a temp file that is automatically removed on shutdown or after a given delay.
	 *
	 * @since 4.0.0
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

			copy( $source, $filename );

		// if not a readable source file, it's most likely just raw data
		} else {

			// create the file
			touch( $filename );

			// open the file, write file, and close it
			$fp = @fopen( $filename, 'w+' );

			@fwrite( $fp, $source );
			@fclose( $fp );
		}

		// make sure the temp file is removed afterwards

		// delay the remove for the given period
		if ( $delay_remove ) {

			if ( ! is_int( $delay_remove ) ) {
				$delay_remove = 60; // default to 60 seconds
			}

			wp_schedule_single_event( time() + $delay_remove, 'wc_customer_order_export_unlink_temp_file', [ $filename ] );
		}

		// ...or simply remove on shutdown
		else {
			$this->temp_filename = $filename;
			register_shutdown_function( [ $this, 'unlink_temp_file' ] );
		}

		return $filename;
	}


	/**
	 * Unlink temp file
	 *
	 * @since 4.0.0
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
	 * Checks if an order was already exported,
	 * globally or for a given export automation.
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order order
	 * @param string|null $automation_id automation ID
	 *
	 * @return bool
	 */
	public function is_order_exported( $order, $automation_id = null ) : bool {

		// if no automation ID is given, check for the global term
		$term = ! empty( $automation_id ) ? Taxonomies_Handler::TERM_PREFIX . $automation_id : Taxonomies_Handler::GLOBAL_TERM;

		return has_term( $term, Taxonomies_Handler::TAXONOMY_NAME_ORDERS, $order->get_id() );
	}


	/**
	 * Marks orders as exported by adding a taxonomy term to the order.
	 *
	 * In 5.0.0 changed $ids to also accept an instance of \WC_Customer_Order_CSV_Export_Export
	 * In 4.0.0 added $ids param as the first param
	 *
	 * @since 3.0.0
	 * @param \WC_Customer_Order_CSV_Export_Export|array $ids a export object or order IDs to mark as exported
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $output_type the output type, either `csv` or `xml`
	 * @param bool $mark_as_exported whether to mark orders as exported
	 * @param bool $add_notes whether to add notes
	 * @param string|null $automation_id automation ID, null for manual exports
	 */
	public function mark_orders_as_exported( $ids, $method = 'download', $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $mark_as_exported = true, $add_notes = true, $automation_id = null ) {

		if ( is_a( $ids, \WC_Customer_Order_CSV_Export_Export::class ) ) {

			$export = $ids;

			$ids              = $export->get_object_ids();
			$method           = $export->get_transfer_method();
			$output_type      = $export->get_output_type();
			$add_notes        = $export->is_note_enabled();
			$mark_as_exported = $export->is_mark_as_exported_enabled();
			$automation_id    = $export->get_automation_id();
		}

		if ( empty( $ids ) ) {
			return;
		}

		$loopback_enabled = $this->get_background_export_handler()->test_connection();

		if ( $loopback_enabled ) {

			// create and dispatch a job to mark orders as exported in the background
			$attrs = [
				'object_ids'       => $ids,
				'method'           => $method,
				'output_type'      => $output_type,
				'mark_as_exported' => $mark_as_exported,
				'add_notes'        => $add_notes,
				'automation_id'    => $automation_id,
			];

			$job_handler = wc_customer_order_csv_export()->get_background_mark_exported_instance();
			$job_handler->create_job( $attrs );
			$job_handler->dispatch();

		} else {

			foreach ( $ids as $order_id ) {
				$this->mark_order_as_exported( $order_id, $method, $output_type, $mark_as_exported, $add_notes, $automation_id );
			}
		}
	}


	/**
	 * Marks an order as exported by adding a taxonomy term to the order.
	 *
	 * @since 5.0.8
	 *
	 * @param int $order_id order ID to mark as exported
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $output_type the output type, either `csv` or `xml`
	 * @param bool $mark_as_exported whether to mark orders as exported
	 * @param bool $add_notes whether to add notes
	 * @param string|null $automation_id automation ID, null for manual exports
	 */
	public function mark_order_as_exported( $order_id, $method = 'download', $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $mark_as_exported = true, $add_notes = true, $automation_id = null ) {

		$order = wc_get_order( $order_id );

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$order_note = $this->add_exported_order_note( $order, $method, $output_type, $add_notes, $automation_id );

		/**
		 * Filters whether to add the "exported" flag to orders or not for the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $mark_as_exported whether to mark the order as exported; defaults to true
		 * @param \WC_Order $order order being exported
		 * @param string $method how the order is exported (ftp, download, etc)
		 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
		 */
		$mark_order_as_exported = apply_filters( "wc_customer_order_export_{$output_type}_mark_order_exported", $mark_as_exported, $order, $method, $this );

		/**
		 * Filters whether to add the "exported" flag to orders or not.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $mark_as_exported whether to mark the order as exported; defaults to true
		 * @param \WC_Order $order order being exported
		 * @param string $method how the order is exported (ftp, download, etc)
		 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
		 */
		if ( apply_filters( 'wc_customer_order_export_mark_order_exported', $mark_order_as_exported, $order, $method, $this ) ) {

			$term = ! empty( $automation_id ) ? Taxonomies_Handler::TERM_PREFIX . $automation_id : Taxonomies_Handler::GLOBAL_TERM;
			wp_add_object_terms( $order->get_id(), $term, Taxonomies_Handler::TAXONOMY_NAME_ORDERS );

			$this->maybe_mark_order_as_globally_exported( $order, $method, $automation_id );
		}

		/**
		 * Fires after an order is exported to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param \WC_Order $order order being exported
		 * @param string $method how the order is exported (ftp, download, etc)
		 * @param string|null $order_note order note message, null if no note was added
		 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
		 */
		do_action( "wc_customer_order_export_{$output_type}_order_exported", $order, $method, $order_note, $this );

		/**
		 * Fires after an order is exported.
		 *
		 * @since 5.0.0
		 *
		 * @param \WC_Order $order order being exported
		 * @param string $method how the order is exported (ftp, download, etc)
		 * @param string|null $order_note order note message, null if no note was added
		 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
		 */
		do_action( 'wc_customer_order_export_order_exported', $order, $method, $order_note, $this );
	}


	/**
	 * Maybe mark order as globally exported when exported by all active automated order exports.
	 *
	 * @since 5.0.9
	 *
	 * @param \WC_Order $order order being exported
	 * @param string $method how the order is exported (ftp, download, etc)
	 * @param string $automation_id automation ID
	 */
	private function maybe_mark_order_as_globally_exported( $order, $method, $automation_id ) {

		if ( ! empty ( $automation_id ) ) {

			/**
			 * Filters whether to mark and order as globally exported when exported by all active automated order exports.
			 *
			 * @since 5.0.9
			 *
			 * @param bool $mark_as_globally_exported whether to mark the order as globally exported; defaults to true
			 * @param \WC_Order $order order being exported
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param string $automation_id automation ID
			 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
			 */
			if ( apply_filters( 'wc_customer_order_export_mark_order_globally_exported', true, $order, $method, $automation_id, $this ) ) {

				$exported_for_all = true;
				$args             = [
					'export_type' => \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
					'enabled'     => true,
				];

				$active_automations = Automation_Factory::get_automations( $args );

				foreach ( $active_automations as $automation ) {

					// skip the current automation
					if ( $automation->get_id() === $automation_id ) {
						continue;
					}

					if ( ! $this->is_order_exported( $order, $automation->get_id() ) ) {

						$exported_for_all = false;
						break;
					}
				}

				if ( $exported_for_all ) {

					$term = Taxonomies_Handler::GLOBAL_TERM;
					wp_add_object_terms( $order->get_id(), $term, Taxonomies_Handler::TAXONOMY_NAME_ORDERS );
				}
			}
		}
	}


	/**
	 * Adds a note to an order, if applicable.
	 *
	 * @since 5.0.8
	 *
	 * @param \WC_Order $order order to add the note
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $output_type the output type, either `csv` or `xml`
	 * @param bool $add_notes whether to add notes
	 * @param string|null $automation_id automation ID, null for manual exports
	 * @return string|null $order_note the note added to the order, or null
	 */
	private function add_exported_order_note( $order, $method = 'download', $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $add_notes = true, $automation_id = null ) {

		$order_note = null;

		// only add order notes if the option is turned on and order has not already been exported
		$add_order_note = $add_notes && ! $this->is_order_exported( $order, $automation_id );

		/**
		 * Filters whether an order note should be added when an order is successfully exported for the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $add_order_note whether an order note should be added
		 */
		$add_order_note = apply_filters( "wc_customer_order_export_{$output_type}_add_order_note", $add_order_note );

		/**
		 * Filters whether an order note should be added when an order is successfully exported.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $add_order_note whether an order note should be added
		 */
		if ( apply_filters( 'wc_customer_order_export_add_order_note', $add_order_note ) ) {

			switch ( $method ) {

				// note that order downloads using the AJAX order action are not marked or noted, only bulk order downloads
				case 'download':
					$order_note = sprintf(
						/* translators: Placeholders: %s - output type (CSV or XML) */
						__( 'Order exported to %s and successfully downloaded.', 'woocommerce-customer-order-csv-export' ),
						strtoupper( $output_type )
					);
				break;

				case 'ftp':
					$order_note = sprintf(
						/* translators: Placeholders: %s - output type (CSV or XML) */
						__( 'Order exported to %s and successfully uploaded to server.', 'woocommerce-customer-order-csv-export' ),
						strtoupper( $output_type )
					);
				break;

				case 'http_post':
					$order_note = sprintf(
						/* translators: Placeholders: %s - output type (CSV or XML) */
						__( 'Order exported to %s and successfully POSTed to remote server.', 'woocommerce-customer-order-csv-export' ),
						strtoupper( $output_type )
					);
				break;

				case 'email':
					$order_note = sprintf(
						/* translators: Placeholders: %s - output type (CSV or XML) */
						__( 'Order exported to %s and successfully emailed.', 'woocommerce-customer-order-csv-export' ),
						strtoupper( $output_type )
					);
				break;

				default:
					$order_note = sprintf(
						/* translators: Placeholders: %s - output type (CSV or XML) */
						__( 'Order exported to %s.', 'woocommerce-customer-order-csv-export' ),
						strtoupper( $output_type )
					);
				break;
			}

			$order->add_order_note( esc_html( $order_note ) );
		}

		return $order_note;
	}


	/**
	 * Checks if a customer was already exported,
	 * globally or for a given export automation.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_User|null $user user
	 * @param WC_Order|null $order order
	 * @param string|null $automation_id automation ID
	 *
	 * @return bool
	 */
	public function is_customer_exported( $user = null, $order = null, $automation_id = null ) {

		$exported = false;

		// if no automation ID is given, check for the global term
		$term = ! empty( $automation_id ) ? Taxonomies_Handler::TERM_PREFIX . $automation_id : Taxonomies_Handler::GLOBAL_TERM;

		if ( ! empty ( $user )
		     // grab the customer user
		     || ( ! empty( $order ) && ! empty( $user = $order->get_user() ) ) ) {

			$exported = has_term( $term, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER, $user );

		} else if ( ! empty ( $order ) ) {

			// guest customer, the term is attached to the order object
			$exported = has_term( $term, Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER, $order );
		}

		return $exported;
	}


	/**
	 * Marks customers as exported by adding a taxonomy term to the user
	 * or to the order if the customer is a guest.
	 *
	 * In 5.0.0 changed $ids to also accept an instance of \WC_Customer_Order_CSV_Export_Export
	 *
	 * @since 4.0.0
	 * @param \WC_Customer_Order_CSV_Export_Export|array $ids a export object or customer IDs to mark as exported. also
	 *                                                        accepts an array of arrays with billing email and order IDs,
	 *                                                        for guest customers: [ $user_id, [ $billing_email, $order_id ] ]
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $output_type the output type, either `csv` or `xml`
	 * @param bool $mark_as_exported whether to mark customers as exported
	 * @param string|null $automation_id automation ID, null for manual exports
	 */
	public function mark_customers_as_exported( $ids, $method = 'download', $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $mark_as_exported = true, $automation_id = null ) {

		if ( is_a( $ids, \WC_Customer_Order_CSV_Export_Export::class ) ) {

			$export = $ids;

			$ids              = $export->get_object_ids();
			$method           = $export->get_transfer_method();
			$output_type      = $export->get_output_type();
			$mark_as_exported = $export->is_mark_as_exported_enabled();
			$automation_id    = $export->get_automation_id();
		}

		foreach ( $ids as $customer_id ) {

			$order_id = $email = $user = null;

			if ( is_array( $customer_id ) ) {

				list( $email, $order_id ) = $customer_id;

				/**
				 * Filters whether to add the "exported" flag to guest customers or not for the given output type.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $mark_as_exported whether to mark the guest as exported; defaults to true
				 * @param int $user_id ID of the customer being exported, 0 for guests
				 * @param string $method how the customer is exported (ftp, download, etc)
				 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
				 */
				$mark_customer_as_exported = apply_filters( "wc_customer_order_export_{$output_type}_mark_customer_exported", $mark_as_exported, 0, $method, $this );

				/**
				 * Filters whether to add the "exported" flag to guest customers or not.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $mark_as_exported whether to mark the guest as exported; defaults to true
				 * @param int $user_id ID of the customer being exported, 0 for guests
				 * @param string $method how the customer is exported (ftp, download, etc)
				 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
				 */
				if ( apply_filters( 'wc_customer_order_export_mark_customer_exported', $mark_customer_as_exported, 0, $method, $this ) ) {

					$term = ! empty( $automation_id ) ? Taxonomies_Handler::TERM_PREFIX . $automation_id : Taxonomies_Handler::GLOBAL_TERM;
					// guest customer, the term is attached to the order object
					wp_add_object_terms( $order_id, $term, Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER );

					$this->maybe_mark_customer_as_globally_exported( $method, $automation_id, null, wc_get_order( $order_id ) );
				}

			} else {

				$user = is_numeric( $customer_id ) ? get_user_by( 'id', $customer_id ) : get_user_by( 'email', $customer_id );

				if ( $user ) {

					$email = $user->user_email;

					/**
					 * Filters whether to add the "exported" flag to guest customers or not for the given output type.
					 *
					 * @since 5.0.0
					 *
					 * @param bool $mark_as_exported whether to mark the guest as exported; defaults to true
					 * @param int $user_id ID of the customer being exported, 0 for guests
					 * @param string $method how the customer is exported (ftp, download, etc)
					 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
					 */
					$mark_customer_as_exported = apply_filters( "wc_customer_order_export_{$output_type}_mark_customer_exported", $mark_as_exported, $user->ID, $method, $this );

					/**
					 * Filters whether to add the "exported" flag to guest customers or not.
					 *
					 * @since 5.0.0
					 *
					 * @param bool $mark_as_exported whether to mark the guest as exported; defaults to true
					 * @param int $user_id ID of the customer being exported, 0 for guests
					 * @param string $method how the customer is exported (ftp, download, etc)
					 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
					 */
					if ( apply_filters( 'wc_customer_order_export_mark_customer_exported', $mark_customer_as_exported, $user->ID, $method, $this ) ) {

						$term = ! empty( $automation_id ) ? Taxonomies_Handler::TERM_PREFIX . $automation_id : Taxonomies_Handler::GLOBAL_TERM;
						wp_add_object_terms( $user->ID, $term, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );

						$this->maybe_mark_customer_as_globally_exported( $method, $automation_id, $user, null );
					}
				}
			}

			$user_id = $user ? $user->ID : null;

			/**
			 * Fires after a customer is exported to the given output type.
			 *
			 * @since 5.0.0
			 *
			 * @param string $email customer email being exported
			 * @param int|null $user_id customer user ID being exported, null if guest customer
			 * @param int|null $order_id related order ID, used for guest customers, may be null if no related order
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
			 */
			do_action( "wc_customer_order_export_{$output_type}_customer_exported", $email, $user_id, $order_id, $method, $this );

			/**
			 * Fires after a customer is exported.
			 *
			 * @since 5.0.0
			 *
			 * @param string $email customer email being exported
			 * @param int|null $user_id customer user ID being exported, null if guest customer
			 * @param int|null $order_id related order ID, used for guest customers, may be null if no related order
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
			 */
			do_action( 'wc_customer_order_export_customer_exported', $email, $user_id, $order_id, $method, $this );
		}
	}


	/**
	 * Maybe mark customer as globally exported when exported by all active automated customer exports.
	 *
	 * @since 5.0.9
	 *
	 * @param string $method how the order is exported (ftp, download, etc)
	 * @param string $automation_id automation ID
	 * @param \WP_User $user customer being exported
	 * @param \WC_Order $order order whose guest customer is being exported
	 */
	private function maybe_mark_customer_as_globally_exported( $method, $automation_id, $user = null, $order = null ) {

		if ( ! empty ( $automation_id ) ) {

			/**
			 * Filters whether to mark a customer as globally exported when exported by all active automated customer exports.
			 *
			 * @since 5.0.9
			 *
			 * @param bool $mark_as_globally_exported whether to mark the customer as globally exported; defaults to true
			 * @param WP_User|null $user customer user being exported
			 * @param WC_Order|null $order order whose guest customer is being exported
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param string $automation_id automation ID
			 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
			 */
			if ( apply_filters( 'wc_customer_order_export_mark_customer_globally_exported', true, $user, $order, $method, $automation_id, $this ) ) {

				$exported_for_all = true;
				$args             = [
					'export_type' => \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS,
					'enabled'     => true,
				];

				$active_automations = Automation_Factory::get_automations( $args );

				foreach ( $active_automations as $automation ) {

					// skip the current automation
					if ( $automation->get_id() === $automation_id ) {
						continue;
					}

					if ( ! $this->is_customer_exported( $user, $order, $automation->get_id() ) ) {

						$exported_for_all = false;
						break;
					}
				}

				if ( $exported_for_all ) {

					$term = Taxonomies_Handler::GLOBAL_TERM;

					if ( ! empty( $user ) ) {

						wp_add_object_terms( $user->ID, $term, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );

					} elseif ( ! empty( $order ) ) {

						// guest customer, the term is attached to the order object
						wp_add_object_terms( $order->get_id(), $term, Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER );
					}
				}
			}
		}
	}


	/**
	 * Replaces variables in file name setting (e.g. %%timestamp%% becomes 2013_03_20_16_22_14 )
	 *
	 * In 5.0.0 added $pre_replace_filename as the first parameter
	 * In 4.0.0 added $ids and $export_type params
	 *
	 * @since 3.0.0
	 *
	 * @param string $pre_replace_filename the configured file name for the export
	 * @param array $ids
	 * @param string $export_type
	 * @param string $output_type
	 * @param string $automation_id automation ID, if any
	 * @return string filename with variables replaced
	 */
	protected function replace_filename_variables( $pre_replace_filename, $ids, $export_type, $output_type, $automation_id = '' ) {

		$variables = [
			'%%timestamp%%' => date( 'Y_m_d_H_i_s', current_time( 'timestamp' ) ),
		];

		if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {
			$variables['%%order_ids%%'] = implode( '-', $ids );
		}

		// replace the "name" placeholder if there is an automation
		if ( $automation_id && $automation = Automation_Factory::get_automation( $automation_id ) ) {
			$variables['%%name%%'] = sanitize_title( $automation->get_name(), '', 'save' );
		}

		/**
		 * Filters the filename merge vars and their replacements for the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $variables associative array of variables and their replacement values
		 * @param array $ids object IDs
		 * @param string $export_type
		 */
		$variables = apply_filters( "wc_customer_order_export_{$output_type}_filename_variables", $variables, $ids, $export_type );

		/**
		 * Filters the filename merge vars and their replacements.
		 *
		 * @since 5.0.0
		 *
		 * @param array $variables associative array of variables and their replacement values
		 * @param array $ids object IDs
		 * @param string $export_type
		 */
		$variables = apply_filters( 'wc_customer_order_export_filename_variables', $variables, $ids, $export_type );

		$post_replace_filename = ! empty( $variables ) ? str_replace( array_keys( $variables ), array_values( $variables ), $pre_replace_filename ) : $pre_replace_filename;

		/**
		 * Filters exported file name
		 *
		 * @since 5.0.0
		 *
		 * @param string $filename filename after replacing variables
		 * @param string $pre_replace_filename filename before replacing variables
		 * @param array $ids object IDs being exported
		 */
		$filename = apply_filters( "wc_customer_order_export_{$output_type}_filename", $post_replace_filename, $pre_replace_filename, $ids );

		/**
		 * Filters exported file name
		 *
		 * @since 5.0.0
		 *
		 * @param string $filename filename after replacing variables
		 * @param string $pre_replace_filename filename before replacing variables
		 * @param array $ids object IDs being exported
		 */
		return apply_filters( 'wc_customer_order_export_filename', $filename, $pre_replace_filename, $ids );
	}


	/**
	 * Get background export handler instance
	 *
	 * Shortcut method for convenience
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Background_Export instance
	 */
	private function get_background_export_handler() {

		return wc_customer_order_csv_export()->get_background_export_instance();
	}


	/**
	 * Get an array of exports
	 *
	 * A simple wrapper around SV_WP_Background_Job_Handler::get_jobs(), for convenience
	 *
	 * @since 4.0.0
	 * @see SV_WP_Background_Job_Handler::get_jobs()
	 * @param array $args Optional. An array of arguments passed to SV_WP_Background_Job_Handler::get_jobs()
	 * @return array Found export objects
	 */
	public function get_exports( $args = [] ) {

		return wc_customer_order_csv_export()->get_background_export_instance()->get_jobs( $args );
	}


	/**
	 * Transfers an export via the given method (FTP, etc).
	 *
	 * @since 4.0.0
	 *
	 * @param string|object $export_id Export (job) instance or ID
	 * @param string $export_method Export method, will default to export's own if not provided
	 * @throws Framework\SV_WC_Plugin_Exception
	 * @throws \Exception
	 */
	public function transfer_export( $export_id, $export_method = null ) {

		$export = wc_customer_order_csv_export_get_export( $export_id );

		if ( ! $export ) {
			/* translators: Placeholders: %s - export ID */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not find export: %s', 'woocommerce-customer-order-csv-export' ), $export_id ) );
		}

		$_export_method = Automation_Factory::get_automation( $export->get_automation_id() )->get_method( [ 'completed_at' => $export->get_completed_at() ] );

		// indicate that the transfer has started
		$export->update_transfer_status( 'processing' );

		// perform the transfer action
		try {

			$_export_method->perform_action( $export );

			$export->update_transfer_status( 'completed' );

			// Mark orders/customers as exported
			if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export->get_type() ) {

				$this->mark_orders_as_exported( $export );

			} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export->get_type() ) {

				$this->mark_customers_as_exported( $export );
			}

		} catch ( Exception $e ) {

			$export->update_transfer_status( 'failed' );

			throw $e;
		}
	}


	/**
	 * Return the filename for test export
	 *
	 * @since 4.0.0
	 *
	 * @param string $output_type 'csv' or 'xml'
	 * @return string
	 */
	protected function get_test_filename( $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {
		return 'test.' . $output_type;
	}


	/**
	 * Return the data (file contents) for test export
	 *
	 * @since 4.0.0
	 *
	 * @param string $output_type 'csv' or 'xml'
	 * @return string
	 */
	protected function get_test_data( $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		$data = '';

		switch ( $output_type ) {
			case WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV:
				$data = "column_1,column_2,column_3\ntest_1,test_2,test_3";
			break;

			case WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML:
				$data = "<?xml version=\"1.0\"?>\n<RootElement>\n<Nodes>\n<Node>\n<Property>Test1</Property>\n</Node>\n<Node>\n<Property>Test2</Property>\n</Node>\n</Nodes>\n</RootElement>";
			break;
		}

		return $data;
	}


	/**
	 * Kick off an export
	 *
	 * @since 4.0.0
	 *
	 * @param int|string|array $ids
	 * @param array $args {
	 *                 An array of arguments
	 *                 @type string $automation_id the ID of an automation associated with the export
	 *                 @type string $type Export type either `orders` or `customers`. Defaults to `orders`
	 *                 @type string $format_key an identifier for a export format definition
	 *                 @type string $method Export transfer method, such as `email`, `ftp`, etc. Defaults to `download`
	 *                 @type string $invocation Export invocation type, used for informational purposes. One of `manual` or `auto`, defaults to `manual`
	 *                 @type string $filename filename for the export (can include merge tags)
	 *                 @type bool $mark_as_exported whether exported objects should be marked as exported and excluded from future exports
	 *                 @type bool $add_notes whether notes should be added to exported objects
	 * }
	 * @return \WC_Customer_Order_CSV_Export_Export|false export object or false on failure
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function start_export( $ids, $args = [] ) {

		// make sure default args are set
		$args = wp_parse_args( $args, [
			'automation_id'    => '',
			'output_type'      => WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV,
			'type'             => WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS,
			'format_key'       => 'default',
			'method'           => 'download',
			'invocation'       => 'manual',
			'filename'         => '',
			'mark_as_exported' => true,
			'add_notes'        => true,
			'batch_enabled'    => false,
		] );

		// handle single order/customer exports
		if ( ! is_array( $ids ) ) {
			$ids = [ $ids ];
		}

		$output_type = $args['output_type'];

		/**
		 * Filters the order/customer/coupon IDs to be exported to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $ids object IDs to be exported
		 * @param array $args export args, see WC_Customer_Order_CSV_Export_Handler::start_export()
		 */
		$ids = apply_filters( "wc_customer_order_export_{$output_type}_ids", $ids, $args );

		/**
		 * Filters the order/customer/coupon IDs to be exported.
		 *
		 * In 4.0.0 removed $this param, added $args param, moved here from WC_Customer_Order_CSV_Export_Generator class
		 *
		 * @since 5.0.0
		 *
		 * @param array $ids object IDs to be exported
		 * @param array $args export args, see WC_Customer_Order_CSV_Export_Handler::start_export()
		 */
		$ids = apply_filters( 'wc_customer_order_export_ids', $ids, $args );

		// no need to export if we have no ids
		if ( empty( $ids ) ) {
			return false;
		}

		$filename = $this->replace_filename_variables( $args['filename'], $ids, $args['type'], $output_type, $args['automation_id'] );
		$filename = $this->maybe_append_filename_extension( $filename, $output_type );

		$export_args = [
			'automation_id'    => $args['automation_id'],
			'object_ids'       => $ids,
			'invocation'       => $args['invocation'],
			'output_type'      => $args['output_type'],
			'type'             => $args['type'],
			'format_key'       => $args['format_key'],
			'method'           => $args['method'],
			'transfer_status'  => null,
			'storage_method'   => 'database',
			'filename'         => $filename,
			'mark_as_exported' => $args['mark_as_exported'],
			'add_notes'        => $args['add_notes'],
			'batch_enabled'    => $args['batch_enabled'],
			'dispatch'         => ! $args['batch_enabled'],
		];

		/**
		 * Filters all the export arguments just before starting a new export to the given output type
		 *
		 * @since 5.0.0
		 *
		 * @param array $export_args the arguments being passed in to this export
		 */
		$export_args = apply_filters( "wc_customer_order_export_start_{$output_type}_export_args", $export_args );

		/**
		 * Filters all the export arguments just before starting a new export.
		 *
		 * @see \WC_Customer_Order_CSV_Export_Export::__construct() for a full list of argument options
		 *
		 * @since 5.0.0
		 *
		 * @param array $export_args the arguments being passed in to this export
		 */
		$export_args = apply_filters( 'wc_customer_order_export_start_export_args', $export_args );

		return new WC_Customer_Order_CSV_Export_Export( $export_args );
	}


	/**
	 * Start an export for the given order if it matches the automation configuration
	 * and was not previously exported by the same automation.
	 *
	 * @since 5.0.0
	 */
	public function maybe_start_export_for_order( \WC_Order $order, Automation $automation ) {

		$should_export = true;

		// bail out if the order is not paid, has already been exported by this automation or is globally exported
		if ( ! $order->is_paid() || $this->is_order_exported( $order, $automation->get_id() ) || Taxonomies_Handler::is_order_exported_globally( $order->get_id() ) ) {
			$should_export = false;
		}

		$order_id             = $order->get_id();
		$product_ids          = $automation->get_product_ids();
		$product_category_ids = $automation->get_product_category_ids();

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/class-wc-customer-order-csv-export-query-parser.php' );

		// bail out if order does not contain required products
		if ( $should_export && ! empty( $product_ids ) ) {

			$order_ids = WC_Customer_Order_CSV_Export_Query_Parser::filter_orders_containing_products( [ $order_id ], $product_ids );

			if ( empty( $order_ids ) ) {
				$should_export = false;
			}
		}

		// bail out if order does not contain products in required categories
		if ( $should_export && ! empty( $product_category_ids ) ) {

			$order_ids = WC_Customer_Order_CSV_Export_Query_Parser::filter_orders_containing_product_categories( [ $order_id ], $product_category_ids );

			if ( empty( $order_ids ) ) {
				$should_export = false;
			}
		}

		/**
		 * Filters whether the order should be exported by this automation.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $should_export whether the order should be exported or not
		 * @param \WC_Order $order the Order object
		 * @param Automation $automation the Automation object
		 */
		if ( apply_filters( 'wc_customer_order_export_automation_should_export_order', $should_export, $order, $automation ) ) {

			try {

				$this->start_export_from_automation( $automation, [ $order_id ] );

			} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

				wc_customer_order_csv_export()->log( sprintf( 'Scheduled automated export failed: %s', $exception->getMessage() ) );
			}
		}
	}


	/**
	 * Start an export as described by an Automation object.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation the Automation object
	 * @param array|null $object_ids overwrite the IDs of objects to export
	 * @return \WC_Customer_Order_CSV_Export_Export|false
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function start_export_from_automation( Automation $automation, $object_ids = null ) {

		$export = false;

		// allow object_ids to be overwritten to export a single object (e.g. an order)
		if ( ! is_array( $object_ids ) ) {
			$object_ids = $automation->get_object_ids();
		}

		if ( ! empty( $object_ids ) ) {

			$output_type = $automation->get_output_type();

			/**
			 * Filters the order IDs that are going to be exported.
			 *
			 * @since 5.0.0
			 *
			 * @param array $object_ids the IDs of the objects that are being exported
			 * @param Automation $automation the Automation object
			 */
			$object_ids = apply_filters( "wc_customer_order_export_{$output_type}_automation_export_ids", $object_ids, $automation );

			/**
			 * Filters the order IDs that are going to be exported.
			 *
			 * @since 5.0.0
			 *
			 * @param array $object_ids the IDs of the objects that are being exported
			 * @param Automation $automation the Automation object
			 */
			$object_ids = apply_filters( 'wc_customer_order_export_automation_export_ids', $object_ids, $automation );

			$args = [
				'automation_id'    => $automation->get_id(),
				'output_type'      => $output_type,
				'type'             => $automation->get_export_type(),
				'format_key'       => $automation->get_format_key(),
				'method'           => $automation->get_method_type(),
				'filename'         => $automation->get_filename(),
				'mark_as_exported' => $automation->is_mark_as_exported_enabled(),
				'add_notes'        => $automation->is_note_enabled(),
				'invocation'       => 'auto',
			];

			if ( $this->is_duplicate_export( $object_ids, $args ) ) {
				return false;
			}

			$export = $this->start_export( $object_ids, $args );
		}

		return $export;
	}


	/**
	 * Determines if a potential new export job is a duplicate of one already in
	 * the queue.
	 *
	 * This serves as a way to combat against cron events being fired multiple
	 * times, as can happen in the wonderful world of WordPress. Here we:
	 *
	 *     1. Generate a fingerprint for the new job based on the args and current time.
	 *     2. Delay processing for a small amount of random time
	 *     3. Check if there are any existing jobs with a matching fingerprint
	 *     4. Whichever concurrent request finished first will block the other from processing
	 *
	 * In 5.0.0 moved from WC_Customer_Order_CSV_Export_Cron
	 *
	 * @param array $object_ids the IDs of the objects that are being exported
	 * @param array $exported the attributes for the export job
	 */
	protected function is_duplicate_export( $object_ids, $export_args ) {

		$timestamp    = time();
		$fingerprint  = $this->generate_auto_export_fingerprint( $object_ids, $export_args, $timestamp );
		$is_duplicate = false;

		// add a random artificial delay
		usleep( rand( 250000, 500000 ) );

		if ( $existing_exports = $this->get_exports( [ 'status' => [ 'queued', 'processing' ], ] ) ) {

			foreach ( $existing_exports as $export ) {

				$export_args = [
					'output_type' => $export->output_type,
					'type'        => $export->type,
					'method'      => $export->method,
					'invocation'  => $export->invocation,
				];

				if ( hash_equals( $fingerprint, $this->generate_auto_export_fingerprint( $export->object_ids, $export_args, $timestamp ) ) ) {
					$is_duplicate = true;
				}
			}
		}

		return $is_duplicate;
	}


	/**
	 * Generates a fingerprint hash for new export jobs to help detect duplicates.
	 *
	 * In 5.0.0 moved from WC_Customer_Order_CSV_Export_Cron
	 *
	 * @since 4.4.0
	 *
	 * @param array $object_ids export job object IDs
	 * @param array $export_args export job args
	 * @param int $timestamp when this fingerprint was generated
	 * @return string
	 */
	protected function generate_auto_export_fingerprint( $object_ids, $export_args, $timestamp ) {

		return md5( json_encode( $object_ids ) . json_encode( $export_args ) . $timestamp );
	}


	/**
	 * Exports a single item.
	 *
	 * This method will normally only ever be called from the background export to
	 * export items one-by-one.
	 *
	 * In 4.0.3 renamed from export_item_to_file to export_item, removed $file_path and
	 * $export_type params, added $export param
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $item Item to export
	 * @param object $job_obj Export (job) associated with the item
	 */
	public function export_item( $item, $job_obj ) {

		if ( $export = wc_customer_order_csv_export_get_export( $job_obj ) ) {

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
	 * @since 4.5.0
	 *
	 * @param array $job_attrs job attributes
	 * @param string $job_id the job ID
	 * @return array job attributes
	 */
	public function export_header( $job_attrs, $job_id ) {

		if ( isset( $job_attrs['type'], $job_attrs['object_ids'], $job_attrs['storage_method'] ) ) {

			// throw in the job ID and emulate a stdClass job object
			$job    = (object) array_merge( $job_attrs, [ 'id' => $job_id ] );
			$export = wc_customer_order_csv_export_get_export( $job );

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
	 * @since 4.0.0
	 * @param object|string $export Export object or ID
	 */
	public function finish_export( $export ) {

		$export = wc_customer_order_csv_export_get_export( $export );

		if ( ! $export ) {
			return;
		}

		if ( 'auto' === $export->get_invocation() ) {

			/**
			 * Fires when objects are auto-exported to the given output type.
			 *
			 * @since 5.0.0
			 *
			 * @param array $object_ids object IDs that were exported
			 */
			do_action( 'wc_customer_order_export_' . $export->get_output_type() . '_' . $export->get_type() . '_exported', $export->get_object_ids() );

			/**
			 * Fires when objects are auto-exported.
			 *
			 * Moved from WC_Customer_Order_CSV_Export_Cron class in 4.0.0.
			 *
			 * @since 5.0.0
			 *
			 * @param array $object_ids object IDs that were exported
			 */
			do_action( 'wc_customer_order_export_' . $export->get_type() . '_exported', $export->get_object_ids() );
		}

		$footer = $export->get_generator()->get_footer();

		$export->store_item( $footer );

		if ( in_array( $export->get_transfer_method(), [ 'download', 'local' ], true ) ) {

			// mark orders/customers as exported
			if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export->get_type() ) {

				$this->mark_orders_as_exported( $export->get_object_ids(), $export->get_transfer_method(), $export->get_output_type(), $export->is_mark_as_exported_enabled(), $export->is_note_enabled(), $export->get_automation_id() );

			} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export->get_type() ) {

				$this->mark_customers_as_exported( $export->get_object_ids(), $export->get_transfer_method(), $export->get_output_type(), $export->is_mark_as_exported_enabled(), $export->get_automation_id() );
			}

		} else {

			try {

				// transfer file via the provided export method
				$this->transfer_export( $export );

			} catch ( Framework\SV_WC_Plugin_Exception $e ) {

				// log errors
				/* translators: Placeholders: %s - error message */
				wc_customer_order_csv_export()->log( sprintf( __( 'Failed to transfer exported file: %s', 'woocommerce-customer-order-csv-export' ), $e->getMessage() ) );
			}
		}

		$this->update_automation_last_run_date( $export, $export->get_completed_at() );

		// Notify the user that the export is complete
		$this->add_export_finished_notice( $export );
	}


	/**
	 * Handles a failed export.
	 *
	 * @since 4.0.0
	 *
	 * @param object|string $export Export job object or ID
	 */
	public function failed_export( $export ) {

		$export = wc_customer_order_csv_export_get_export( $export );

		if ( ! $export ) {
			return;
		}

		$this->update_automation_last_run_date( $export, $export->get_failed_at() );

		$this->add_export_finished_notice( $export );
	}


	/**
	 * Sets the automation last run date to the date the export was completed or failed.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export $export Export object
	 * @param string $last_run representation of the date the export was completed or failed
	 */
	private function update_automation_last_run_date( \WC_Customer_Order_CSV_Export_Export $export, $last_run ) {

		if ( $export->get_automation_id() && $automation = Automation_Factory::get_automation( $export->get_automation_id() ) ) {

			$automation->set_last_run( strtotime( $last_run ) - wc_timezone_offset() );
			$automation->save();
		}
	}


	/**
	 * Add export finished notice for a user
	 *
	 * @since 4.0.0
	 * @param \WC_Customer_Order_CSV_Export_Export|object|string $export Export object or ID
	 */
	public function add_export_finished_notice( $export ) {

		if ( ! $export instanceof WC_Customer_Order_CSV_Export_Export ) {
			$export = wc_customer_order_csv_export_get_export( $export );
		}

		// don't notify if no export found
		if ( ! $export ) {
			return;
		}

		// Notify the user that the manual export failed
		if ( 'manual' === $export->get_invocation() ) {

			$message_id = 'wc_customer_order_export_finished_' . $export->get_id();

			// add notice for manually created exports
			if ( $export->get_created_by() && ! wc_customer_order_csv_export()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $export->get_created_by() ) ) {

				$export_notices = get_user_meta( $export->get_created_by(), '_wc_customer_order_export_notices', true );

				if ( ! $export_notices ) {
					$export_notices = [];
				}

				$export_notices[] = $export->get_id();

				update_user_meta( $export->get_created_by(), '_wc_customer_order_export_notices', $export_notices );
			}

		}

		$successful = 'completed' === $export->get_status() && ( ! $export->get_transfer_status() || 'completed' === $export->get_transfer_status() );

		// Notify admins that automatic exports are failing
		if ( ! $successful && 'auto' === $export->get_invocation() ) {

			$failure_type      = 'failed' === $export->get_status() ? 'export' : 'transfer';
			$failure_notices   = get_option( 'wc_customer_order_export_' . $export->get_output_type() . '_failure_notices', [] );
			$multiple_failures = ! empty( $failure_notices ) && ! empty( $failure_notices[ $failure_type ] );

			$failure_notices[ $failure_type ] = [
				'export_id'         => $export->get_id(),
				'multiple_failures' => $multiple_failures,
			];

			update_option( 'wc_customer_order_export_' . $export->get_output_type() . '_failure_notices', $failure_notices );
		}

	}


	/**
	 * Removes export finished notice from user meta.
	 *
	 * @since 4.0.0
	 *
	 * @param object|string $export Export object or ID
	 * @param int $user_id
	 */
	public function remove_export_finished_notice( $export, $user_id ) {

		$export = wc_customer_order_csv_export_get_export( $export );

		if ( ! $export || ! $user_id ) {
			return;
		}

		$export_id = $export->get_id();

		$export_notices = get_user_meta( $user_id, '_wc_customer_order_export_notices', true );

		if ( ! empty( $export_notices ) && in_array( $export_id, $export_notices, true ) ) {

			unset( $export_notices[ array_search( $export_id, $export_notices ) ] );

			update_user_meta( $user_id, '_wc_customer_order_export_notices', $export_notices );
		}

		// also remove the message from user dismissed notices
		$dismissed_notices = wc_customer_order_csv_export()->get_admin_notice_handler()->get_dismissed_notices( $user_id );
		$message_id        = 'wc_customer_order_export_finished_' . $export_id;

		if ( ! empty( $dismissed_notices ) && isset( $dismissed_notices[ $message_id ] ) ) {
			unset( $dismissed_notices[ $message_id ] );

			update_user_meta( $user_id, '_wc_plugin_framework_customer_order_csv_export_dismissed_messages', $dismissed_notices );
		}
	}


	/**
	 * Removes expired exports.
	 *
	 * Deletes completed/failed exports older than the maximum age (14 days by default)
	 *
	 * @since 4.0.0
	 */
	public function remove_expired_exports() {

		$args = [
			'status' => [ 'completed', 'failed' ],
		];

		// get all completed or failed jobs
		$all_jobs = $this->get_exports( $args );

		if ( empty( $all_jobs ) ) {
			return;
		}

		// loop over the jobs and find those that should be removed
		foreach ( $all_jobs as $job ) {

			$date = 'completed' === $job->status ? $job->completed_at : $job->failed_at;

			// job completed/failed within the max age timeframe, remove it (along with the file)
			if ( strtotime( $date ) <= current_time( 'timestamp' ) - $this->get_export_max_age() ) {

				$export = wc_customer_order_csv_export_get_export( $job );

				if ( $export ) {

					$export->delete();
				}
			}
		}
	}


	/**
	 * Gets the maximum age of stored exports, in seconds.
	 *
	 * @since 4.6.3
	 *
	 * @return int
	 */
	public function get_export_max_age() {

		/**
		 * Filters the maximum age of stored exports.
		 *
		 * @since 4.6.3
		 *
		 * @param int $max_age the maximum age of stored exports, in seconds
		 */
		return (int) apply_filters( 'wc_customer_order_export_start_export_max_age', 14 * DAY_IN_SECONDS );
	}


	/**
	 * Appends the filename extension if it's missing.
	 *
	 * @since 5.2.0
	 *
	 * @param string $filename
	 * @param string $output_type
	 * @return string
	 */
	private function maybe_append_filename_extension( $filename, $output_type ) {

		// determines whether the extension will be appended automatically to the filename
		$will_append_extension = '' === pathinfo( $filename, PATHINFO_EXTENSION );

		/**
		 * Filters whether the extension may be automatically appended if missing or not.
		 *
		 * @since 5.2.0
		 *
		 * @param bool $will_append_extension whether the extension will be automatically appended
		 * @param string $filename the output filename
		 * @param string $output_type the filename type which will be appended as its extension
		 * @param \WC_Customer_Order_CSV_Export_Handler $handler handler instance
		 */
		return apply_filters( 'wc_customer_order_export_append_extension_if_missing', $will_append_extension, $filename, $output_type, $this )
			? sprintf( '%s.%s', $filename, $output_type )
			: $filename;
	}


}
