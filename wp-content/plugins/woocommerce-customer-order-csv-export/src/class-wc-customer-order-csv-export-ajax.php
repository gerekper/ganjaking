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
 * Customer/Order CSV Export AJAX Handler
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_AJAX {


	/**
	 * Initialize AJAX class instance
	 *
	 * @since 4.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_customer_order_export_create_export',     [ $this, 'create_export' ] );
		add_action( 'wp_ajax_wc_customer_order_export_get_export_status', [ $this, 'get_export_status' ] );

		// filter out grouped products from WC JSON search results
		add_filter( 'woocommerce_json_search_found_products', [ $this, 'filter_json_search_found_products' ] );

		// handle dismissed admin notices
		add_action( 'wc_customer_order_export_dismiss_notice', [ $this, 'handle_dismiss_notice' ], 10, 2 );

		// toggle an automation via AJAX
		add_action( 'wp_ajax_wc_customer_order_export_admin_toggle_automation', [ $this, 'ajax_toggle_automation' ] );
	}


	/**
	 * Create export job
	 *
	 * @since 4.0.0
	 */
	public function create_export() {

		check_ajax_referer( 'create-export', 'security' );

		try {

			if ( empty( $_POST['export_query'] ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Missing export query.', 'woocommerce-customer-order-csv-export' ) );
			}

			$export_query = $_POST['export_query'];
			$object_ids   = ! empty( $export_query['ids'] ) ? $export_query['ids'] : [];

			if ( ! empty( $_POST['automation_id'] ) ) {

				$automation = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automation( $_POST['automation_id'] );

				if ( ! $automation ) {
					throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid automated export', 'woocommerce-customer-order-csv-export' ) );
				}

				$export = wc_customer_order_csv_export()->get_export_handler_instance()->start_export_from_automation( $automation, $object_ids );

			} else {

				// bail out if no export type, method, query or output type provided
				if ( empty( $_POST['export_type'] ) || empty( $_POST['export_method'] ) || empty( $_POST['output_type'] ) ) {
					throw new Framework\SV_WC_Plugin_Exception( __( 'Missing export type, method, or output type.', 'woocommerce-customer-order-csv-export' ) );
				}

				$export_type      = $_POST['export_type'];
				$export_method    = $_POST['export_method'];
				$output_type      = $_POST['output_type'];
				$export_format    = Framework\SV_WC_Helper::get_posted_value( 'export_format' );
				$filename         = wc_clean( wp_unslash( Framework\SV_WC_Helper::get_posted_value( 'filename' ) ) );
				$mark_as_exported = (bool) Framework\SV_WC_Helper::get_posted_value( 'mark_as_exported' );
				$add_notes        = (bool) Framework\SV_WC_Helper::get_posted_value( 'add_notes' );
				$batch_enabled    = (bool) Framework\SV_WC_Helper::get_posted_value( 'batch_enabled' );

				require_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/class-wc-customer-order-csv-export-query-parser.php' );

				// `ids` in the query will take priority - it's used directly, as the export
				// input and all other query params will be ignored
				$object_ids = ! empty( $object_ids ) ? $object_ids : WC_Customer_Order_CSV_Export_Query_Parser::parse_export_query( $export_query, $export_type, $output_type );

				// in case we're exporting a single order, cast as array
				$object_ids = array_filter( array_map( [ $this, 'sanitize_export_ids' ], (array) $object_ids ) );

				// save settings for the next manual export of this type for faster set up in the future
				if ( $user_id = get_current_user_id() ) {
					update_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_{$export_type}_manual_export_format", $export_format );
					update_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_{$export_type}_manual_export_filename", $filename );
					update_user_meta( $user_id, "_wc_customer_order_export_{$output_type}_manual_export_add_order_notes", (int) $add_notes );
					// general setting for manual exports of all types
					update_user_meta( $user_id, "_wc_customer_order_export_manual_export_batch_enabled", (int) $batch_enabled );
				}

				// nothing found to export
				if ( empty( $object_ids ) ) {

					switch ( $export_type ) {

						case WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
							$message = __( 'No orders found to export', 'woocommerce-customer-order-csv-export' );
							break;

						case WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
							$message = __( 'No customers found to export', 'woocommerce-customer-order-csv-export' );
							break;

						case WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:
							$message = __( 'No coupons found to export.', 'woocommerce-customer-order-csv-export' );
							break;

						default:
							$message = __( 'No items found to export.', 'woocommerce-customer-order-csv-export' );
					}

					throw new Framework\SV_WC_Plugin_Exception( $message, 404 );
				}

				$export = wc_customer_order_csv_export()->get_export_handler_instance()->start_export( $object_ids, [
					'output_type'      => $output_type,
					'type'             => $export_type,
					'format_key'       => $export_format ?: 'default',
					'method'           => $export_method,
					'filename'         => $filename ?: "{$export_type}-export-%%timestamp%%.{$output_type}",
					'mark_as_exported' => $mark_as_exported,
					'add_notes'        => $add_notes,
					'batch_enabled'    => $batch_enabled,
				] );
			}

			if ( ! $export ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'An error occurred.', 'woocommerce-customer-order-csv-export' ) );
			}

			// construct the status url
			$status_url = wp_nonce_url( admin_url( 'admin-ajax.php' ), 'get-export-status', 'security' );
			$status_url = add_query_arg( [
				'action'    => 'wc_customer_order_export_get_export_status',
				'export_id' => $export->get_id(),
			], $status_url );

			wp_send_json( [
				'export_id'  => $export->get_id(),
				'method'     => $export->get_transfer_method(),
				'status'     => $export->get_status(),
				'status_url' => $status_url,
			] );

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$title = 404 === $exception->getCode() ? __( 'Nothing to Export', 'woocommerce-customer-order-csv-export' ) : __( 'Export Failed', 'woocommerce-customer-order-csv-export' );

			wp_send_json_error( [
				'title'   => esc_html( $title ),
				'message' => esc_html( $exception->getMessage() ),
			] );
		}
	}


	/**
	 * Ensure export IDs are only integers. Note that customer export IDs
	 * can be either a user ID or for guests, an array in the format: [ billing email, order ID ]
	 *
	 * @since 4.3.3
	 * @param $id
	 * @return array|int
	 */
	public function sanitize_export_ids( $id ) {

		if ( is_array( $id ) ) {
			return [ wc_clean( $id[0] ), absint( $id[1] ) ];
		} else {
			return absint( $id );
		}
	}


	/**
	 * Get export job status
	 *
	 * @since 4.0.0
	 */
	public function get_export_status() {

		check_ajax_referer( 'get-export-status', 'security' );

		// Bail out if no export id is provided
		if ( empty( $_GET['export_id'] ) ) {
			return;
		}

		$export = wc_customer_order_csv_export_get_export( $_GET['export_id'] );

		try {

			if ( ! $export ) {
				throw new Framework\SV_WC_Plugin_Exception( wc_customer_order_csv_export()->get_background_export_instance()->get_export_status_message( 'not-found', $_GET['export_id'] ) );
			}

			if ( 'failed' === $export->get_status() ) {
				throw new Framework\SV_WC_Plugin_Exception( wc_customer_order_csv_export()->get_background_export_instance()->get_export_status_message( 'failed' ) );
			}

			$response = [
				'export_id'        => $export->get_id(),
				'method'           => $export->get_transfer_method(),
				'mark_as_exported' => $export->is_mark_as_exported_enabled(),
				'status'           => $export->get_status(),
				'transfer_status'  => $export->get_transfer_status(),
			];

			if ( 'completed' === $export->get_status() ) {

				$download_url = wp_nonce_url( admin_url(), 'download-export' );

				// return the download url for the exported file
				$response['download_url'] = add_query_arg( [
					'download_exported_file' => 1,
					'export_id'              => $response['export_id'],
				], $download_url );

				if ( 'failed' === $export->get_transfer_status() ) {
					throw new Framework\SV_WC_Plugin_Exception( wc_customer_order_csv_export()->get_background_export_instance()->get_export_status_message( 'transfer-failed' ) );
				}
			}

			wp_send_json_success( $response );

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$data = $export ? [ 'id' => $export->get_id() ] : [];

			$data['message'] = $e->getMessage();

			wp_send_json_error( $data );
		}
	}


	/**
	 * Selects the given export format in the settings.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 * @deprecated 5.0.0
	 */
	public function select_export_format() {

		wc_deprecated_function( __METHOD__, '5.0.0' );
	}


	/**
	 * Remove grouped products from json search results
	 *
	 * @since 4.0.0
	 * @param array $products
	 * @return array $products
	 */
	public function filter_json_search_found_products( $products ) {

		// Remove grouped products
		if ( isset( $_REQUEST['exclude'] ) && 'wc_customer_order_csv_export_grouped_products' === $_REQUEST['exclude'] ) {
			foreach( $products as $id => $title ) {

				$product = wc_get_product( $id );

				if ( $product->is_type('grouped') ) {
					unset( $products[ $id ] );
				}
			}
		}

		return $products;
	}


	/**
	 * Handle dismissing admin notices
	 *
	 * Removes any export finished or auto-export failure notices from db
	 *
	 * @since 4.0.0
	 * @param string $message_id
	 * @param int $user_id
	 */
	public function handle_dismiss_notice( $message_id, $user_id ) {

		$auto_export_failure_message_ids = [
			'wc_customer_order_export_csv_auto_export_failure',
			'wc_customer_order_export_csv_auto_export_transfer_failure',
			'wc_customer_order_export_csv_auto_export_failure',
			'wc_customer_order_export_csv_auto_export_transfer_failure'
		];

		// user-specific notices (used for manual exports)
		if ( Framework\SV_WC_Helper::str_starts_with( $message_id, 'wc_customer_order_export_finished_' ) ) {

			$parts     = explode( '_', $message_id );
			$export_id = array_pop( $parts );

			wc_customer_order_csv_export()->get_export_handler_instance()->remove_export_finished_notice( $export_id, $user_id );

		} elseif ( ! in_array( $message_id, $auto_export_failure_message_ids, true ) ) {
			return;
		}

		// auto-export failure notices
		$failure_type = Framework\SV_WC_Helper::str_ends_with( $message_id, 'transfer_failure' ) ? 'transfer' : 'export';
		$output_type  = strpos( $message_id, WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML ) !== false ? WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML : WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV;
		$notices      = get_option( 'wc_customer_order_export_' . $output_type . '_failure_notices' );

		unset( $notices[ $failure_type ] );

		update_option( 'wc_customer_order_export_' . $output_type . '_failure_notices', $notices );

		// undismiss notice, so that if further failures happen, the notice will re-appear
		wc_customer_order_csv_export()->get_admin_notice_handler()->undismiss_notice( $message_id, $user_id );
	}


	/**
	 * Toggles an automation via AJAX.
	 *
	 * @internal
	 *
	 * @since 5.0.0
	 */
	public function ajax_toggle_automation() {

		$new_state = isset( $_POST['new_state'] ) ? (bool) $_POST['new_state'] : null;

		try {

			if ( ! wp_verify_nonce( Framework\SV_WC_Helper::get_posted_value( 'nonce' ), 'wc_customer_order_export_admin_toggle_automation' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Please try again.', 'woocommerce-customer-order-csv-export' ) );
			}

			$automation = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automation( Framework\SV_WC_Helper::get_posted_value( 'automation_id' ) );

			if ( ! $automation || ! $automation->get_id() ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Not found.', 'woocommerce-customer-order-csv-export' ) );
			}

			if ( null === $new_state ) {
				$new_state = ! $automation->is_enabled( 'edit' );
			}

			$automation->set_enabled( $new_state );
			$automation->save();

			wp_send_json_success();

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			if ( $new_state ) {
				$action = __( 'enable', 'woocommerce-customer-order-csv-export' );
			} else if ( null !== $new_state ) {
				$action = __( 'disable', 'woocommerce-customer-order-csv-export' );
			} else {
				$action = __( 'toggle', 'woocommerce-customer-order-csv-export' );
			}

			$message = sprintf(
				__( 'Could not %1$s automated export. %2$s', 'woocommerce-customer-order-csv-export' ),
				$action,
				$exception->getMessage()
			);

			wp_send_json_error( $message );
		}

		exit;
	}


}
