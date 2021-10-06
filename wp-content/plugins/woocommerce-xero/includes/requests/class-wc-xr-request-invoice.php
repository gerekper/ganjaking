<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, WC_XR_Invoice $invoice ) {
		$settings = apply_filters( 'woocommerce_xero_invoice_request_settings', $settings, $invoice );
		$invoice->settings = $settings;
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_endpoint( 'Invoices' );

		$settings = new WC_XR_Settings();
		if ( $settings->get_option( 'four_decimals' ) === 'on' ) {
			$this->set_query( array( 'unitdp' => '4' ) );
		}

		// Set the XML
		$this->set_body( '<Invoices>' . $invoice->to_xml() .'</Invoices>' );
		$this->set_method( 'POST' );

	}

}
