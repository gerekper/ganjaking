<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Admin
 * @author    WooThemes
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre-Orders Admin Orders class.
 */
class WC_Pre_Orders_Admin_Orders {

	/**
	 * Initialize the admin order actions.
	 */
	public function __construct() {
		// Add pre-order emails to list of available emails to resend.
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'maybe_allow_resend_of_pre_order_emails' ) ); // < 3.2
		add_filter( 'woocommerce_order_actions', array( $this, 'maybe_allow_resend_of_pre_order_emails' ) ); // >= 3.2

		// Hook to make sure pre order is properly set up when added through admin.
		add_action( 'save_post', array( $this, 'check_manual_order_for_pre_order_products' ), 10, 1 );
	}

	/**
	 * Add pre-order emails to the list of order emails that can be resent, based on the pre-order status.
	 *
	 * @param  array $available_emails Simple array of WC_Email class IDs that can be resent.
	 *
	 * @return array
	 */
	public function maybe_allow_resend_of_pre_order_emails( $available_emails ) {
		global $theorder;

		$emails = array();

		if ( WC_Pre_Orders_Order::order_contains_pre_order( $theorder ) ) {

			$emails[] = 'wc_pre_orders_pre_ordered';

			$pre_order_status = WC_Pre_Orders_Order::get_pre_order_status( $theorder );

			if ( 'cancelled' === $pre_order_status ) {
				$emails[] = 'wc_pre_orders_pre_order_cancelled';
			}

			if ( 'completed' === $pre_order_status ) {
				$emails[] = 'wc_pre_orders_pre_order_available';
			}
		}

		// If we're using 3.2 or above, convert the emails to the specific structure.
		if ( version_compare( WC_VERSION, '3.2', '>=' ) ) {
			$mailer = WC()->mailer();
			$mails  = $mailer->get_emails();
			$new_emails = array();

			foreach ( $mails as $mail ) {
				if ( in_array( $mail->id, $emails ) && 'no' !== $mail->enabled ) {
					$new_emails[ 'send_email_' . esc_attr( $mail->id ) ] = sprintf( __( 'Resend %s', 'woocommerce-pre-orders' ), esc_html( $mail->title ) );
				}
			}

			$emails = $new_emails;
		}

		return array_merge( $available_emails, $emails );
	}

	/**
	 * Marks the order as being a pre order if it contains pre order products in
	 * case an order gets added manually from the administration panel.
	 *
	 * @param int $order_id ID of the newly saved order.
	 *
	 * @since 1.4.10
	 * @version 1.5.3
	 */
	public function check_manual_order_for_pre_order_products( $order_id ) {
		// Make sure we are in the administration panel and we're saving an order.
		if ( ! is_admin() || ! isset( $_POST['post_type'] ) || 'shop_order' != $_POST['post_type'] ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Check if the order hasn't been processed already.
		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
			return;
		}

		// Order has not been processed yet (or doesn't contain pre orders).
		$contains_pre_orders = false;

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			$product = null;
			if ( is_array( $item ) && isset( $item['item_meta']['_product_id'][0] ) ) {
				$product = wc_get_product( $item['item_meta']['_product_id'][0] );
			} elseif ( is_object( $item ) && is_callable( array( $item, 'get_product' ) ) ) {
				$product = $item->get_product();
			}

			if ( ! $product ) {
				continue;
			}

			if ( 'yes' === get_post_meta( $product->get_id(), '_wc_pre_orders_enabled', true ) ) {
				// Set correct flags for this order, making it a pre order.
				update_post_meta( $order_id, '_wc_pre_orders_is_pre_order', 1 );
				update_post_meta( $order_id, '_wc_pre_orders_when_charged', get_post_meta( $product->get_id(), '_wc_pre_orders_when_to_charge', true ) );
				return;
			}
		}
	}
}

new WC_Pre_Orders_Admin_Orders();
