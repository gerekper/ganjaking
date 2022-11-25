<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Contact extends WC_XR_Request {

	/**
	 * Constructor
	 *
	 * @param WC_XR_Settings $settings Xero settings object.
	 * @param string         $email    Contact email.
	 */
	public function __construct( WC_XR_Settings $settings, $email, $name = '' ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );
		$this->set_endpoint( 'Contacts' );
		if ( ! empty( $name ) ) {
			$this->set_query(
				array(
					'where' => 'Name=="' . $name . '"',
				)
			);
		} else {
			$this->set_query(
				array(
					'where' => 'EmailAddress=="' . $email . '"',
				)
			);
		}
	}

}
