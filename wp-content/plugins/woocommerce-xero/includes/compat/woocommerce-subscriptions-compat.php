<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility class for WooCommerce Subscriptions
 */
class Woocommerce_Subscriptions_Compat extends WC_XR_Invoice_Manager {
	/**
	 * Constructor
	 */
	public function __construct( WC_XR_Settings $settings ) {
		parent::__construct( $settings );

		$this->settings = $settings;

		add_filter( 'wcs_new_order_created', array( $this, 'order_created' ), 10, 3 );
		add_filter( 'wcs_renewal_order_created', array( $this, 'renewal_order_created' ), 10 );
	}

	public function order_created( $order, $subscription, $type ) {
		$option = $this->settings->get_option( 'send_invoices' );

		if ( 'creation' === $option && 'renewal_order' !== $type ) {
			$this->send_invoice( $order->get_id() );
		}

		return $order;
	}

	/**
	 * Send Invoice to Xero for Subscription renewal order (If send invoices on order creation is selected).
	 *
	 * @param WC_Order $renewal_order Subscription renewal order.
	 */
	public function renewal_order_created( $renewal_order ) {
		$option = $this->settings->get_option( 'send_invoices' );

		if ( 'creation' === $option ) {
			$this->send_invoice( $renewal_order->get_id() );
		}

		return $renewal_order;
	}
}
