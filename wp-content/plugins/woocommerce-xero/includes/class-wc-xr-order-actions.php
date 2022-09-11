<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Order_Actions {

	/**
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Order_Actions constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Setup the required WooCommerce hooks
	 */
	public function setup_hooks() {

		// Add order actions
		add_action( 'woocommerce_order_actions', array( $this, 'add_order_actions' ) );

		// Catch order actions
		add_action( 'woocommerce_order_action_xero_manual_invoice', array( $this, 'manual_invoice' ) );
		add_action( 'woocommerce_order_action_xero_manual_payment', array( $this, 'manual_payment' ) );
	}

	/**
	 * Add order actions
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function add_order_actions( $actions ) {

		// This should never happen but yeah let's check it anyway
		if ( ! is_array( $actions ) ) {
			$actions = array();
		}

		$actions['xero_manual_invoice'] = __( 'Send Invoice to Xero', 'woocommerce-xero' );
		$actions['xero_manual_payment'] = __( 'Send Payment to Xero', 'woocommerce-xero' );

		return $actions;
	}

	/**
	 * Handle the order actions callback for creating a manual invoice
	 *
	 * @param WC_Order $order
	 *
	 * @return boolean
	 */
	public function manual_invoice( $order ) {

		// Invoice Manager
		$invoice_manager = new WC_XR_Invoice_Manager( $this->settings );

		// Send Invoice
		$order_id = $order->get_id();
		$invoice_manager->send_invoice( $order_id );

		return true;
	}

	/**
	 * Handle the order actions callback for creating a manual payment
	 *
	 * @param WC_Order $order
	 *
	 * @return boolean
	 */
	public function manual_payment( $order ) {

		if( 0 == $order->get_total() ) {
			$order->add_order_note( __( 'Xero invoice amount is zero, no payment necessary.', 'woocommerce-xero' ) );
			return false;
		}

		// Payment Manager.
		$payment_manager = new WC_XR_Payment_Manager( $this->settings );

		// Send Payment.
		$order_id = $order->get_id();
		$payment_manager->send_payment( $order_id );

		return true;
	}
}
