<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// https://developer.xero.com/documentation/api/tax-rates

class WC_XR_Request_Tax_Rate extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );
		$this->set_endpoint( 'TaxRates' );
		// Note that set_query + where we used to use is not working correctly
		// with the xero api and should no longer be used
	}

}
