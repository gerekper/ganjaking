<?php
/**
 * Orders functionality
 *
 * This class handles the subscription process in the orders.
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Orders.
 */
class WC_Newsletter_Subscription_Orders {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_changed', array( $this, 'process_order_status_changed' ), 10, 4 );
	}

	/**
	 * Processes the order status changed.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $order_id    The order id.
	 * @param string   $from_status Status transition from.
	 * @param string   $to_status   Status transition to.
	 * @param WC_Order $order       Order object.
	 */
	public function process_order_status_changed( $order_id, $from_status, $to_status, $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// If the order's subscription has been processed.
		if ( $order->get_meta( '_newsletter_subscription' ) !== '1' ) {
			return;
		}

		// Checks if the status order allow the subscription.
		$allowed_statuses = get_option( 'woocommerce_newsletter_order_statuses' );
		if ( ! empty( $allowed_statuses ) && ! in_array( 'wc-' . $to_status, $allowed_statuses, true ) ) {
			return;
		}

		$this->process_order_subscription_actions( $order );

		// Prevents resubscribe.
		$order->delete_meta_data( '_newsletter_subscription' );
		$order->save_meta_data();
	}

	/**
	 * Processes the order subscription actions.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 */
	private function process_order_subscription_actions( $order ) {
		$billing = $order->get_address( 'billing' );

		$args = array(
			'first_name' => $billing['first_name'],
			'last_name'  => $billing['last_name'],
		);

		if ( wc_newsletter_subscription_provider_supports( 'tags' ) ) {
			$args['tags'] = wc_newsletter_subscription_get_tags_for_order( $order );
		}

		wc_newsletter_subscription_subscribe( $billing['email'], $args );
	}
}
return new WC_Newsletter_Subscription_Orders();
