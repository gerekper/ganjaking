<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Organisation extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );
		$this->set_endpoint( 'Organisation' );
	}

}
