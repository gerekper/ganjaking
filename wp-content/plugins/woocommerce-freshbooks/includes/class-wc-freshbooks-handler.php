<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Handler class
 *
 * Handles webhook actions (updating invoices, payments and clearing transients)
 * and invoice workflow actions like applying an invoice payment when an order
 * is paid
 *
 * @since 3.0
 */
class WC_FreshBooks_Handler {


	/**
	 * Add actions
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// handle single order invoice create/send from order action button
		if ( is_admin() ) {
			add_action( 'wp_ajax_wc_freshbooks_create_and_send_invoice', array( $this, 'process_ajax_create_and_send_invoice' ) );
		}

		// actions for auto-creating invoices & payments
		$this->add_workflow_actions();

		// handle invoice events
		add_action( 'wc_freshbooks_webhook_invoice', array( $this, 'handle_invoice' ), 10, 2 );

		// handle payment events (primarily create & delete)
		add_action( 'wc_freshbooks_webhook_payment', array( $this, 'handle_payment' ), 10, 2 );

		// clear active client/item transients when a related webhook is received
		add_action( 'wc_freshbooks_webhook_client', array( $this, 'clear_clients_transient' ) );
		add_action( 'wc_freshbooks_webhook_item', array( $this, 'clear_items_transient' ) );
	}


	/**
	 * Process the Ajax create & send invoice order action on the Orders screen
	 *
	 * @since 3.0
	 */
	public function process_ajax_create_and_send_invoice() {

		if ( ! is_admin() || ! current_user_can( 'edit_posts' ) ) {

			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-freshbooks' ) );
		}

		if ( ! check_admin_referer( 'wc_freshbooks_create_and_send_invoice' ) ) {

			wp_die( __( 'You have taken too long, please go back and try again.', 'woocommerce-freshbooks' ) );
		}

		$order_id = ! empty( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : '';

		if ( ! $order_id ) {
			die;
		}

		$order = new \WC_FreshBooks_Order( $order_id );

		$order->create_invoice();

		wp_redirect( wp_get_referer() );

		exit;
	}


	/** Workflow methods ******************************************************/


	/**
	 * Add actions for auto-creating invoices & applying invoice payments
	 *
	 * @since 3.0
	 */
	public function add_workflow_actions() {

		if ( 'yes' === get_option( 'wc_freshbooks_auto_create_invoices' ) ) {

			// auto-create invoices for both orders with successful payment & those
			// paid with cheque or similar gateways that place the order on-hold
			add_action( 'woocommerce_payment_complete',                array( $this, 'auto_create_invoice' ) );
			add_action( 'woocommerce_order_status_pending_to_on-hold', array( $this, 'auto_create_invoice' ) );

			// COD gateway uses pending > processing ಠ_ಠ
			add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'auto_create_invoice' ) );
		}

		if ( 'yes' === get_option( 'wc_freshbooks_auto_apply_payments' ) ) {

			// apply invoice payment when payment is received is received successfully
			add_action( 'woocommerce_payment_complete', array( $this, 'auto_apply_invoice_payment' ), 20 );

			// apply invoice payment for gateways that do not call WC_Order::payment_complete()
			add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'auto_apply_invoice_payment' ) );
			add_action( 'woocommerce_order_status_on-hold_to_completed',  array( $this, 'auto_apply_invoice_payment' ) );

			// apply invoice payment when order payment has previously failed
			add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'auto_apply_invoice_payment' ) );
			add_action( 'woocommerce_order_status_failed_to_completed',  array( $this, 'auto_apply_invoice_payment' ) );
		}
	}


	/**
	 * Automatically creates an invoice.
	 *
	 * @internal
	 *
	 * @since 3.0
	 *
	 * @param string $order_id WC order ID
	 */
	public function auto_create_invoice( $order_id ) {

		$order = new \WC_FreshBooks_Order( $order_id );

		$order->create_invoice();
	}


	/**
	 * Automatically applies invoice payment.
	 *
	 * @since 3.0
	 *
	 * @param string $order_id WC order ID
	 */
	public function auto_apply_invoice_payment( $order_id ) {

		$order = new \WC_FreshBooks_Order( $order_id );

		$order->apply_invoice_payment();
	}


	/** Webhook methods ******************************************************/


	/**
	 * Handles invoice webhook events.
	 *
	 * @internal
	 * @link http://developers.freshbooks.com/docs/callbacks/#events
	 *
	 * @since 3.0
	 *
	 * @param string $action the event type
	 * @param string $invoice_id the associated invoice ID
	 */
	public function handle_invoice( $action, $invoice_id ) {

		$order_id = $this->get_object_by_freshbooks_id( 'order', 'invoice', $invoice_id );

		if ( ! $order_id ) {
			return;
		}

		$order = new \WC_FreshBooks_Order( $order_id );

		if ( 'delete' === $action ) {

			// there's no way to actually delete an invoice in freshbooks, they can
			// always be undeleted at a later date, so we'll simply change
			// the status to a custom status which can be easily updated later
			// if the admin undeletes it
			$order->update_meta_data( '_wc_freshbooks_invoice_status', 'deleted' );
			$order->save_meta_data();

		} else {

			$order->refresh_invoice();
		}
	}


	/**
	 * Handles payment webhook events.
	 *
	 * @internal
	 * @link http://developers.freshbooks.com/docs/callbacks/#events
	 *
	 * @since 3.0
	 *
	 * @param string $action the event type
	 * @param string $payment_id the associated payment ID
	 */
	public function handle_payment( $action, $payment_id ) {

		try {

			// try to find matching order by payment ID
			$order_id = $this->get_object_by_freshbooks_id( 'order', 'payment', $payment_id );

			// if a payment was created in FreshBooks manually, look up the associated invoice first
			if ( ! $order_id ) {

				$payment = wc_freshbooks()->get_api()->get_payment( $payment_id );

				// no payment found
				if ( ! $payment ) {
					return;
				}

				// invoice IDs included as part of the payment response contain
				// leading zeroes for some odd reason, so strip them first
				$order_id = $this->get_object_by_freshbooks_id( 'order', 'invoice', ltrim( $payment['invoice_id'], '0' ) );

				// no matching invoice found, likely deleted
				if ( ! $order_id ) {
					return;
				}
			}

			$order = new \WC_FreshBooks_Order( $order_id );

			if ( 'delete' === $action ) {

				$order->delete_meta_data( '_wc_freshbooks_payment_id' );

			} else {

				$order->update_meta_data( '_wc_freshbooks_payment_id', $payment_id );
			}

			$order->save_meta_data();

			$order->refresh_invoice();

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( sprintf( 'Payment handling failed: %s', $e->getMessage() ) );
		}
	}


	/**
	 * Clear the active clients transient when a client is changed in FreshBooks
	 *
	 * @since 3.0
	 */
	public function clear_clients_transient() {

		delete_transient( 'wc_freshbooks_active_clients' );
	}


	/**
	 * Clear the active items transient when an item is changed in FreshBooks
	 *
	 * @since 3.0
	 */
	public function clear_items_transient() {

		delete_transient( 'wc_freshbooks_active_items' );
	}


	/**
	 * Get a WC Object (product/order/user) from a FreshBooks object ID, this helps
	 * ease the process of looking up a user, order, or product given an ID
	 * for a resource in FreshBooks
	 *
	 * @since 3.0
	 * @param string $object_type WC object, either `product`, `order`, or `user`
	 * @param string $id_type FreshBooks ID type, either `invoice`, `client`, `payment` or `item`
	 * @param string $id FreshBooks ID for the given type
	 * @return \WC_FreshBooks_Order|\WC_Product|\WP_User|null
	 */
	private function get_object_by_freshbooks_id( $object_type, $id_type, $id ) {
		global $wpdb;

		$object_id = null;

		$meta_key = "_wc_freshbooks_{$id_type}_id";

		// users
		if ( 'user' === $object_type ) {

			$object_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
					$meta_key,
					$id
				)
			);

			// products/orders
		} else {

			$object_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
					$meta_key,
					$id
				)
			);
		}

		return $object_id;
	}


}
