<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Update_Contact extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $contact_id, $contact ) {
		parent::__construct( $settings );

		$contact->set_id( $contact_id );

		$this->set_method( 'POST' );
		$this->set_endpoint( 'Contacts' );
		$this->set_body( $contact->to_xml() );
	}

}
