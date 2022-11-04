<?php
/**
 * Privacy data cleanup code.
 *
 * @package WooCommerce Xero/Privacy
 */

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * Class WC_XR_Privacy
 *
 * Handles export and removal of privacy-related customer data.
 */
class WC_XR_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( __( 'Xero', 'woocommerce-xero' ) );

		$this->add_exporter( 'woocommerce-xero-order-data', __( 'WooCommerce Xero Order Data', 'woocommerce-xero' ), array( $this, 'order_data_exporter' ) );

		if ( function_exists( 'wcs_get_subscriptions' ) ) {
			$this->add_exporter( 'woocommerce-xero-subscriptions-data', __( 'WooCommerce Xero Subscriptions Data', 'woocommerce-xero' ), array( $this, 'subscriptions_data_exporter' ) );
		}

		$this->add_eraser( 'woocommerce-xero-order-data', __( 'WooCommerce Xero Data', 'woocommerce-xero' ), array( $this, 'order_data_eraser' ) );
	}

	/**
	 * Returns a list of orders.
	 *
	 * @param string $email_address Email address whose orders are returned.
	 * @param int    $page Page of orders to return.
	 *
	 * @return array WP_Post
	 */
	protected function get_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query = array(
			'limit' => 10,
			'page'  => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}

		return wc_get_orders( $order_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		/* translators: %s: URL to privacy page on woocommerce.com */
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-xero' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-xero' ) );
	}

	/**
	 * Handle exporting data for Orders.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function order_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$orders = $this->get_orders( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_orders',
					'group_label' => __( 'Orders', 'woocommerce-xero' ),
					'item_id'     => 'order-' . $order->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Xero payment id', 'woocommerce-xero' ),
							'value' => $order->get_meta( '_xero_payment_id' ),
						),
						array(
							'name'  => __( 'Xero invoice id', 'woocommerce-xero' ),
							'value' => $order->get_meta( '_xero_invoice_id' ),
						),
					),
				);
			}

			$done = 10 > count( $orders );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Handle exporting data for Subscriptions.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function subscriptions_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$page           = (int) $page;
		$data_to_export = array();

		$meta_query = array(
			array(
				'key'     => '_billing_email',
				'value'   => $email_address,
				'compare' => '=',
			),
		);

		$subscription_query = array(
			'posts_per_page' => 10,
			'page'           => $page,
			'meta_query'     => $meta_query,
		);

		$subscriptions = wcs_get_subscriptions( $subscription_query );

		$done = true;

		if ( 0 < count( $subscriptions ) ) {
			foreach ( $subscriptions as $subscription ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_subscriptions',
					'group_label' => __( 'Subscriptions', 'woocommerce-xero' ),
					'item_id'     => 'subscription-' . $subscription->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Xero payment id', 'woocommerce-xero' ),
							'value' => $subscription->get_meta( '_xero_payment_id' ),
						),
						array(
							'name'  => __( 'Xero invoice id', 'woocommerce-xero' ),
							'value' => $subscription->get_meta( '_xero_invoice_id' ),
						),
					),
				);
			}

			$done = 10 > count( $subscriptions );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_orders( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_order( $order );
			$items_removed                    |= $removed;
			$items_retained                   |= $retained;
			$messages                          = array_merge( $messages, $msgs );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_subscription( $order );
			$items_removed                    |= $removed;
			$items_retained                   |= $retained;
			$messages                          = array_merge( $messages, $msgs );
		}

		// Tell core if we have more orders to work on still.
		$done = count( $orders ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to Subscriptions
	 *
	 * @param WC_Subscription $order Subscription to handle.
	 * @return array
	 */
	protected function maybe_handle_subscription( $order ) {
		if ( ! class_exists( 'WC_Subscriptions' ) && ! class_exists( 'WC_Subscriptions_Core_Plugin' ) ) {
			return array( false, false, array() );
		}

		if ( ! wcs_order_contains_subscription( $order ) ) {
			return array( false, false, array() );
		}

		$subscription    = current( wcs_get_subscriptions_for_order( $order->get_id() ) );
		$xero_payment_id = $subscription->get_meta( '_xero_payment_id' );

		if ( empty( $xero_payment_id ) ) {
			return array( false, false, array() );
		}

		/**
		 * Filter Subscription statuses for which the Xero meta data will be erased.
		 *
		 * @since 1.7.11
		 * @param array(string) List of Order/Subscription statuses.
		 */
		if ( $subscription->has_status( apply_filters( 'wc_xero_privacy_eraser_subs_statuses', array( 'on-hold', 'active' ) ) ) ) {
			/* translators: %d: Order id  */
			return array( false, true, array( sprintf( __( 'Order ID %d contains an active Subscription' ), $order->get_id() ) ) );
		}

		$renewal_orders = WC_Subscriptions_Renewal_Order::get_renewal_orders( $order->get_id() );

		foreach ( $renewal_orders as $renewal_order_id ) {
			// TODO: this might need an update based on how Subscriptions handle COT migration.
			delete_post_meta( $renewal_order_id, '_xero_payment_id' );
			delete_post_meta( $renewal_order_id, '_xero_invoice_id' );
		}

		$subscription->delete_meta_data( '_xero_payment_id' );
		$subscription->delete_meta_data( '_xero_invoice_id' );
		$subscription->save_meta_data();

		return array( true, false, array( __( 'Xero Subscription Data Erased.', 'woocommerce-xero' ) ) );
	}

	/**
	 * Handle eraser of data tied to Orders
	 *
	 * @param WC_Order $order Order to handle.
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		$xero_payment_id = $order->get_meta( '_xero_payment_id' );
		$xero_invoice_id = $order->get_meta( '_xero_invoice_id' );

		if ( empty( $xero_payment_id ) && empty( $xero_invoice_id ) && empty( $xero_currencyrate ) ) {
			return array( false, false, array() );
		}

		$order->delete_meta_data( '_xero_payment_id' );
		$order->delete_meta_data( '_xero_invoice_id' );
		$order->save_meta_data();

		return array( true, false, array( __( 'Xero personal data erased.', 'woocommerce-xero' ) ) );
	}
}
