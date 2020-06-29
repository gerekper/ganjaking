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
 * Customer/Order XML Export Suite AJAX Handler
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_AJAX {


	/**
	 * Initialize AJAX class instance
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_customer_order_xml_export_suite_create_export',     array( $this, 'create_export' ) );
		add_action( 'wp_ajax_wc_customer_order_xml_export_suite_get_export_status', array( $this, 'get_export_status' ) );

		// filter out grouped products from WC JSON search results
		add_filter( 'woocommerce_json_search_found_products', array( $this, 'filter_json_search_found_products' ) );

		// handle dismissed admin notices
		add_action( 'wc_customer_order_xml_export_suite_dismiss_notice', array( $this, 'handle_dismiss_notice' ), 10, 2 );
	}


	/**
	 * Create export job
	 *
	 * @since 2.0.0
	 */
	public function create_export() {

		check_ajax_referer( 'create-export', 'security' );

		// bail out if no export type, method or query provided
		if ( empty( $_POST['export_type'] ) || empty( $_POST['export_method'] ) || empty( $_POST['export_query'] ) ) {

			wp_send_json_error( array(
				'title'   => esc_html__( 'Export Failed', 'woocommerce-customer-order-xml-export-suite' ),
				'message' => esc_html__( 'Missing export type, method or query.', 'woocommerce-customer-order-xml-export-suite' ),
			) );
		}

		$export_type   = $_POST['export_type'];
		$export_method = $_POST['export_method'];
		$export_query  = $_POST['export_query'];

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-query-parser.php' );

		// `ids` in the query will take priority - it's used directly, as the export
		// input and all other query params will be ignored
		$export_ids = ! empty( $export_query['ids'] ) ? $export_query['ids'] : WC_Customer_Order_XML_Export_Suite_Query_Parser::parse_export_query( $export_query, $export_type );

		// in case we're exporting a single order, cast as array
		$export_ids = array_map( array( $this, 'sanitize_export_ids' ), (array) $export_ids );

		// nothing found to export
		if ( empty( $export_ids ) ) {

			switch ( $export_type ) {

				case 'orders':
					$message = esc_html__( 'No orders found to export', 'woocommerce-customer-order-xml-export-suite' );
				break;

				case 'customers':
					$message = esc_html__( 'No customers found to export', 'woocommerce-customer-order-xml-export-suite' );
				break;

				case 'coupons':
					$message = esc_html__( 'No coupons found to export', 'woocommerce-customer-order-xml-export-suite' );
				break;
			}

			wp_send_json_error( array(
				'title'   => esc_html__( 'Nothing to Export', 'woocommerce-customer-order-xml-export-suite' ),
				'message' => $message,
			) );
		}

		if ( 'auto_export' === $export_method ) {

			$export_method = wc_customer_order_xml_export_suite()->get_methods_instance()->get_auto_export_method( $export_type );

			if ( ! $export_method ) {

				wp_send_json_error( array(
					'title'   => esc_html__( 'Export Failed', 'woocommerce-customer-order-xml-export-suite' ),
					'message' => esc_html__( 'Auto export method not configured.', 'woocommerce-customer-order-xml-export-suite' ),
				) );
			}
		}

		try {

			$export = wc_customer_order_xml_export_suite()->get_export_handler_instance()->start_export( $export_ids, array(
				'type'   => $export_type,
				'method' => $export_method,
			) );

			// construct the status url
			$status_url = wp_nonce_url( admin_url( 'admin-ajax.php' ), 'get-export-status', 'security' );
			$status_url = add_query_arg( array(
				'action'    => 'wc_customer_order_xml_export_suite_get_export_status',
				'export_id' => $export->get_id(),
			), $status_url );

			wp_send_json( array(
				'export_id'  => $export->get_id(),
				'method'     => $export->get_transfer_method(),
				'status'     => $export->get_status(),
				'status_url' => $status_url,
			) );

		} catch ( SV_WC_Plugin_Exception $e ) {

			wc_customer_order_xml_export_suite()->log( sprintf( esc_html__( 'Export Failed: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );

			wp_send_json_error( array(
				'title'   => esc_html__( 'Export Failed', 'woocommerce-customer-order-xml-export-suite' ),
				'message' => $e->getMessage(),
			) );

		}
	}


	/**
	 * Ensure export IDs are only integers. Note that customer export IDs
	 * can be either a user ID or for guests, an array in the format: array( billing email, order ID )
	 *
	 * @since 2.2.0-dev.1
	 * @param $id
	 * @return array|int
	 */
	public function sanitize_export_ids( $id ) {
		if ( is_array( $id ) ) {
			return array( wc_clean( $id[0] ), absint( $id[1] ) );
		} else {
			return absint( $id );
		}
	}


	/**
	 * Get export job status
	 *
	 * @since 2.0.0
	 */
	public function get_export_status() {

		check_ajax_referer( 'get-export-status', 'security' );

		// Bail out if no export id is provided
		if ( empty( $_GET['export_id'] ) ) {
			return;
		}

		$export = wc_customer_order_xml_export_suite_get_export( $_GET['export_id'] );

		try {

		if ( ! $export ) {

			$error = wc_customer_order_xml_export_suite()->get_background_export_instance()->get_export_status_message( 'not-found', $_GET['export_id'] );

			wc_customer_order_xml_export_suite()->log( $error );
			throw new SV_WC_Plugin_Exception( $error );
		}

		if ( 'failed' === $export->get_status() ) {

			$error = wc_customer_order_xml_export_suite()->get_background_export_instance()->get_export_status_message( 'failed' );

			wc_customer_order_xml_export_suite()->log( $error );
			throw new SV_WC_Plugin_Exception( $error );
		}

		$response = array(
			'export_id'       => $export->get_id(),
			'method'          => $export->get_transfer_method(),
			'status'          => $export->get_status(),
			'transfer_status' => $export->get_transfer_status(),
		);

		if ( 'completed' === $export->get_status() ) {

			$download_url = wp_nonce_url( admin_url(), 'download-export' );

			// return the download url for the exported file
			$response['download_url'] = add_query_arg( array(
				'download_exported_xml_file' => 1,
				'export_id'                  => $export->get_id(),
			), $download_url );

			if ( 'failed' === $export->get_transfer_status() ) {

				$error = wc_customer_order_xml_export_suite()->get_background_export_instance()->get_export_status_message( 'transfer-failed' );
				wc_customer_order_xml_export_suite()->log( $error );
				throw new SV_WC_Plugin_Exception( $error );
			}
		}

			wp_send_json_success( $response );

		} catch ( SV_WC_Plugin_Exception $e ) {

			$data = $export ? array( 'id' => $export->get_id() ) : array();

			$data['message'] = $e->getMessage();

			wp_send_json_error( $data );
		}
	}


	/**
	 * Remove grouped products from json search results
	 *
	 * @since 2.0.0
	 * @param array $products
	 * @return array $products
	 */
	public function filter_json_search_found_products( $products ) {

		// Remove grouped products
		if ( isset( $_REQUEST['exclude'] ) && 'wc_customer_order_xml_export_suite_grouped_products' === $_REQUEST['exclude'] ) {
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
	 * @since 2.0.0
	 * @param string $message_id
	 * @param int $user_id
	 */
	public function handle_dismiss_notice( $message_id, $user_id ) {

		// user-specific notices (used for manual exports)
		if ( SV_WC_Helper::str_starts_with( $message_id, 'wc_customer_order_xml_export_suite_finished_' ) ) {

			$parts     = explode( '_', $message_id );
			$export_id = array_pop( $parts );

			wc_customer_order_xml_export_suite()->get_export_handler_instance()->remove_export_finished_notice( $export_id, $user_id );

		} elseif ( ! in_array( $message_id, array( 'wc_customer_order_xml_export_suite_auto_export_failure', 'wc_customer_order_xml_export_suite_auto_export_transfer_failure' ), true ) ) {
			return;
		}

		// auto-export failure notices
		$failure_type = SV_WC_Helper::str_ends_with( $message_id, 'transfer_failure' ) ? 'transfer' : 'export';
		$notices      = get_option( 'wc_customer_order_xml_export_suite_failure_notices' );

		unset( $notices[ $failure_type ] );

		update_option( 'wc_customer_order_xml_export_suite_failure_notices', $notices );

		// undismiss notice, so that if further failures happen, the notice will re-appear
		wc_customer_order_xml_export_suite()->get_admin_notice_handler()->undismiss_notice( $message_id, $user_id );
	}


}
