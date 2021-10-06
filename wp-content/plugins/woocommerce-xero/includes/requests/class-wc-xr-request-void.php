<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Void extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, WC_XR_Invoice $invoice, $xml ) {
		$settings = apply_filters( 'woocommerce_xero_invoice_request_settings', $settings, $invoice );
		$invoice->settings = $settings;
		parent::__construct( $settings );

		// Set Endpoint.
		$this->set_endpoint( 'Invoices' );

		// Must be a POST call not a PUT call.
		$this->set_method( 'POST' );

		// Set the XML.
		$this->set_body( $xml );

	}
}
