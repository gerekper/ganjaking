<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use ACP\Access\ActivationUpdater;
use ACP\ActivationTokenFactory;
use ACP\Nonce\LicenseNonce;
use ACP\RequestHandler;

class LicenseUpdate implements RequestHandler {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var ActivationUpdater
	 */
	private $activation_updater;

	public function __construct( ActivationTokenFactory $activation_token_factory, ActivationUpdater $activation_updater ) {
		$this->activation_token_factory = $activation_token_factory;
		$this->activation_updater = $activation_updater;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new LicenseNonce() )->verify( $request ) ) {
			return;
		}

		$token = $this->activation_token_factory->create();

		if ( ! $token ) {
			$this->error_notice( 'Missing activation token.' );

			return;
		}

		$api_response = $this->activation_updater->update( $token );

		if ( $api_response && $api_response->has_error() ) {
			$this->error_notice( $api_response->get_error()->get_error_message() );

			return;
		}

		$this->success_notice( __( 'License information has been updated.', 'codepress-admin-columns' ) );
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Message::ERROR )->register();
	}

	private function success_notice( $message ) {
		( new Notice( $message ) )->register();
	}

}