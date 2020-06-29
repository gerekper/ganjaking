<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Integration
 */
class WC_Account_Funds_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_pdf_invoice_order_status', array( $this, 'skip_sending_invoice' ), 10, 2 );
	}

	/**
	 * Don't send an invoice for deposits
	 * @param  array $order_statuses
	 * @param  int $order_id
	 * @return array
	 */
	public function skip_sending_invoice( $order_statuses, $order_id ) {
		if ( WC_Account_Funds_Order_Manager::order_contains_deposit( $order_id ) ) {
			return array();
		}
		return $order_statuses;
	}
}

new WC_Account_Funds_Integration();