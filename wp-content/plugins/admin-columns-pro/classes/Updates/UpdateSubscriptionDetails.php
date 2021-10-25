<?php
namespace ACP\Updates;

use ACP\API\Request;
use ACP\License;
use ACP\RequestDispatcher;

class UpdateSubscriptionDetails {

	/** @var License */
	private $license;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	public function __construct( License $license, RequestDispatcher $api ) {
		$this->license = $license;
		$this->api = $api;
	}

	public function update() {
		$response = $this->api->dispatch( new Request\SubscriptionDetails( $this->license->get_key() ) );

		if ( $response->get( 'expiry_date' ) ) {
			$this->license->set_expiry_date( $response->get( 'expiry_date' ) );
		}

		if ( $response->get( 'renewal_discount' ) ) {
			$this->license->set_renewal_discount( $response->get( 'renewal_discount' ) );
		}

		if ( $response->get( 'renewal_method' ) ) {
			$this->license->set_renewal_method( $response->get( 'renewal_method' ) );
		}

		if ( $response->get( 'status' ) ) {
			$this->license->set_status( $response->get( 'status' ) );
		}

		$this->license->save();
	}

}