<?php

namespace ACP\RequestHandler;

use AC\Message\Notice;
use ACP\API;

class LicenseUpdate extends SubscriptionDetails {

	public function handle( API\Request\SubscriptionDetails $request ) {
		$response = parent::handle( $request );

		if ( $response && $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		$this->success_notice( __( 'License information has been updated.', 'codepress-admin-columns' ) );
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Notice::ERROR )->register();
	}

	private function success_notice( $message ) {
		( new Notice( $message ) )->register();
	}

}