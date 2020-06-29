<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * AJAX class
 *
 * Handles ajax callbacks in admin or front end
 *
 * @since 3.0.0
 */
class WC_PIP_Ajax {


	/**
	 * Add ajax actions
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// handle individual order actions and bulks actions
		add_action( 'wp_ajax_wc_pip_confirm_order_action', [ $this, 'confirm_order_action' ] );
		add_action( 'wp_ajax_wc_pip_process_order_action', [ $this, 'process_order_action' ] );
	}


	/**
	 * Sends modal data to confirm an order action.
	 *
	 * @internal
	 *
	 * @since 3.7.1
	 */
	public function confirm_order_action() {

		check_ajax_referer( 'confirm-order-action', 'security' );

		$order_ids = isset( $_POST['order_ids'] ) ? array_unique( array_map( 'absint', (array) $_POST['order_ids'] ) ) : [];
		$action    = isset( $_POST['document'] ) ? trim( $_POST['document'] ) : '';
		$actions   = array_keys( array_merge( wc_pip()->get_orders_instance()->get_actions(), wc_pip()->get_orders_instance()->get_bulk_actions() ) );
		$type      = '';
		$response  = [];

		try {

			if ( '' === $action ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Missing action' );
			}

			if ( ! in_array( $action, $actions, true ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid action' );
			}

			if ( empty( $order_ids ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Missing order IDs to process' );
			}

			foreach ( array_keys( wc_pip()->get_document_types() ) as $document_type ) {

				if ( false !== strpos( str_replace( '_', '-', $action ), $document_type ) ) {

					$type = $document_type;
					break;
				}
			}

			if ( '' === $type ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid document type' );
			}

			if ( false !== strpos( $action, 'print' ) ) {
				$action = 'print';
			} elseif ( false !== strpos( $action, 'send_email' ) ) {
				$action = 'send_email';
			}

			$document = wc_pip()->get_document( $type, [ 'order_id' => current( $order_ids ), 'order_ids' => $order_ids ] );

			if ( ! $document ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Document not found or invalid' );
			}

			$orders_count = count( $order_ids );

			if ( 'send_email' === $action ) {

				$heading = sprintf(
					/* translators: Placeholder: %s - document name */
					_n( 'Send %s', 'Send %s', $orders_count, 'woocommerce-pip' ),
					1 === $orders_count ? $document->name : $document->name_plural
				);

				if ( 'pick-list' === $document->type ) {
					$message = sprintf(
						/* translators: Placeholder: %d - orders count, %s - document name */
						_n( 'Ready to send %s for %d order by email.', 'Ready to send %s for %d orders by email.', $orders_count, 'woocommerce-pip' ),
						$document->name,
						$orders_count
					);
				} else {
					$message = sprintf(
						/* translators: Placeholder: %d - orders count, %s - document name */
						_n( 'Ready to send %d %s by email.', 'Ready to send %d %s by email.', $orders_count, 'woocommerce-pip' ),
						$orders_count,
						strtolower( 1 === $orders_count ? $document->name : $document->name_plural )
					);
				}

				$response = [
					'type'    => $type,
					'action'  => $action,
					'orders'  => $order_ids,
					'heading' => $heading,
					'message' => $message,
					'url'     => '#',
					'label'   => _n( 'Send Email', 'Send Emails', $orders_count, 'woocommerce-pip' ),
				];

			} elseif ( 'print' === $action ) {

				$order_ids_hash = md5( json_encode( $order_ids ) );

				// Save the order IDs into an option.
				// Initially we were using a transient, but this seemed to cause issues on some hosts (mainly GoDaddy) that had difficulty in implementing a proper object cache override.
				update_option( "wc_pip_order_ids_{$order_ids_hash}", $order_ids );

				/** @see \WC_PIP_Print::print_document_action() */
				$action_url = wp_nonce_url(
					add_query_arg(
						[
							'wc_pip_action'   => 'print',
							'wc_pip_document' => $document->type,
							'order_id'        => current( $order_ids ),
							'order_ids'       => $order_ids_hash,
						],
						admin_url( 'edit.php?post_type=shop_order' )
					),
					'wc_pip_document'
				);

				$heading = sprintf(
					/* translators: Placeholder: %s - document name */
					_n( 'Print %s', 'Print %s', $orders_count, 'woocommerce-pip' ),
					1 === $orders_count ? $document->name : $document->name_plural
				);

				if ( 'pick-list' === $document->type ) {
					$message = sprintf(
						/* translators: Placeholder: %d - orders count, %s - document name */
						_n( 'Ready to print %s for %d order.', 'Ready to print %s for %d orders.', $orders_count, 'woocommerce-pip' ),
						$document->name,
						$orders_count
					);
				} else {
					$message = sprintf(
						/* translators: Placeholder: %d - orders count, %s - document name */
						_n( 'Ready to print %d %s.', 'Ready to print %d %s.', $orders_count, 'woocommerce-pip' ),
						$orders_count,
						strtolower( 1 === $orders_count ? $document->name : $document->name_plural )
					);
				}

				$response = [
					'type'    => $type,
					'action'  => $action,
					'orders'  => $order_ids,
					'heading' => $heading,
					'message' => $message,
					'url'     => $action_url,
					'label'   => _n( 'Print Document', 'Print Documents', $orders_count, 'woocommerce-pip' ),
				];

			}

			wp_send_json_success( array_merge( $response, [ 'done' => false ] ) );

		} catch ( Framework\SV_WC_Plugin_Exception $error ) {

			wp_send_json_error( [ $error->getMessage() => [
				'document_type' => $type,
				'action'        => $action,
				'order_ids'     => $order_ids,
				'done'          => false,
			] ] );
		}
	}


	/**
	 * Processes order actions.
	 *
	 * @internal
	 *
	 * @since 3.7.1
	 */
	public function process_order_action() {

		check_ajax_referer( 'process-order-action', 'security' );

		$type      = isset( $_POST['document_type'] )   ? (string) $_POST['document_type']   : '';
		$action    = isset( $_POST['document_action'] ) ? (string) $_POST['document_action'] : '';
		$orders    = [];
		$processed = 0;
		$message   = $heading = '';

		try {

			if ( '' === $type ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Undefined document type' );
			}

			if ( '' === $action ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Undefined document action' );
			}

			if ( empty( $_POST['order_ids'] ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'No order IDs' );
			}

			$orders    = is_string( $_POST['order_ids'] ) ? explode( ',', $_POST['order_ids'] ) : (array) $_POST['order_ids'];
			$order_ids = array_unique( array_map( 'absint', $orders ) );

			if ( 'send_email' === $action ) {

				if ( in_array( $type, [ 'invoice', 'packing-list' ], true ) ) {

					foreach ( $order_ids as $order_id ) {

						$document = wc_pip()->get_document( $type, [ 'order_id' => $order_id ] );

						if ( $document ) {

							/**
							 * Fires when sending an email manually from the orders edit screens.
							 *
							 * This is meant to force enable emails that are normally disabled.
							 *
							 * @see \WC_PIP_Email_Invoice::is_enabled()
							 * @see \WC_PIP_Email_Packing_List::is_enabled()
							 *
							 * @since 3.5.0
							 *
							 * @param \WC_PIP_Document $document related document object
							 */
							do_action( 'wc_pip_sending_manual_order_email', $document );

							$document->send_email();

							$processed++;
						}
					}

				} elseif ( 'pick-list' === $type ) {

					$document = wc_pip()->get_document( $type, [ 'order_id' => current( $order_ids ), 'order_ids' => $order_ids ] );

					if ( $document ) {

						/* this action is documented in class-wc-pip-orders-admin.php */
						do_action( 'wc_pip_sending_manual_order_email', $document );

						$document->send_email();

						$processed++;
					}
				}

				/**
				 * Fires after emails are sent via manual action.
				 *
				 * @since 3.0.0
				 *
				 * @param string $type document type
				 * @param int[] $order_ids array of order IDs
				 */
				do_action( 'wc_pip_process_orders_bulk_action_send_email', $type, $order_ids );

				if ( 0 === $processed ) {
					/* translators: Placeholder: %s - document name (plural) */
					$heading = isset( $document ) ? sprintf( __( 'No %s sent', 'woocommerce-pip' ), $document->name_plural ) : __( 'No Emails Sent', 'woocommerce-pip' );
					$message = __( 'No emails sent.', 'woocommerce-pip' );
				} else {
					/* translators: Placeholder: %s - document name (singular or plural) */
					$heading = isset( $document ) ? sprintf( _n( '%s sent', '%s sent', $processed, 'woocommerce-pip' ), 1 === $processed ? $document->name : $document->name_plural ) : __( 'Emails Sent', 'woocommerce-pip' );
					/* translators: Placeholder: %d - number of documents printed */
					$message = sprintf( _n( '%d email sent.', '%d emails sent.', $processed, 'woocommerce-pip' ), $processed );
				}

			} elseif ( 'print' !== $action ) {

				$document = wc_pip()->get_document( $type, [ 'order_id' => current( $order_ids ), 'order_ids' => $order_ids ] );

				/**
				 * Fires after an order action is processed.
				 *
				 * Third parties can hook here to process custom actions.
				 *
				 * @since 3.0.0
				 *
				 * @param string $action_type action to be performed
				 * @param \WC_PIP_Document $document document object
				 */
				do_action( 'wc_pip_process_orders_bulk_action', $action, $document );

				$processed = count( $order_ids );

				if ( isset( $document ) ) {
					/* translators: Placeholder: %s - document name (singular or plural) */
					$heading = sprintf( _n( 'Print %s', 'Print %s', $processed, 'woocommerce-pip' ), 1 === $processed ? $document->name : $document->name_plural );
				} else {
					/* translators: Placeholder: %s - document name (singular or plural) */
					$heading = _n( 'Print Document', 'Print Documents', $processed, 'woocommerce-pip' );
				}

				if ( 0 === $processed ) {
					$message = __( 'No printable documents.', 'woocommerce-pip' );
				} else {
					/* translators: Placeholder: %d - number of documents printed */
					$message = sprintf( _n( '%d document printed.', '%d documents printed.', $processed, 'woocommerce-pip' ), $processed );
				}
			}

			wp_send_json_success( [
				'heading' => $heading,
				'message' => $message,
				'done'    => true,
			] );

		} catch ( Framework\SV_WC_Plugin_Exception $error ) {

			wp_send_json_error( [ $error->getMessage() => [
				'document_type' => $type,
				'action'        => $action,
				'order_ids'     => $orders,
				'done'          => false,
			] ] );
		}
	}


	/**
	 * Sends email for order.
	 *
	 * TODO remove this deprecated method by August 2020 or by version 4.0.0 whichever comes first {FN 2019-07-30}
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * @deprecated since 3.7.1
	 */
	public function order_send_email() {

		wc_deprecated_function( __METHOD__, '3.7.1', __CLASS__ . '::process_order_action()' );

		wp_send_json_error( 'Deprecated AJAX action. Use "confirm_order_action".' );
	}


}
