<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationStorage;
use ACP\Access\PermissionChecker;
use ACP\Access\Rule\ApiDeactivateResponse;
use ACP\ActivationTokenFactory;
use ACP\API;
use ACP\LicenseKeyRepository;
use ACP\Nonce;
use ACP\RequestDispatcher;
use ACP\RequestHandler;
use ACP\Type\SiteUrl;
use ACP\Updates\ProductsUpdater;

class LicenseDeactivate implements RequestHandler {

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var ActivationKeyStorage
	 */
	private $activation_key_storage;

	/**
	 * @var ActivationStorage
	 */
	private $activation_storage;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var ProductsUpdater
	 */
	private $products_updater;

	/**
	 * @var PermissionChecker
	 */
	private $permission_checker;

	public function __construct( LicenseKeyRepository $license_key_repository, ActivationKeyStorage $activation_key_storage, ActivationStorage $activation_storage, RequestDispatcher $api, SiteUrl $site_url, ActivationTokenFactory $activation_token_factory, ProductsUpdater $products_updater, PermissionChecker $permission_checker ) {
		$this->license_key_repository = $license_key_repository;
		$this->activation_key_storage = $activation_key_storage;
		$this->activation_storage = $activation_storage;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->activation_token_factory = $activation_token_factory;
		$this->products_updater = $products_updater;
		$this->permission_checker = $permission_checker;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\LicenseNonce() )->verify( $request ) ) {
			return;
		}

		$token = $this->activation_token_factory->create();

		$this->license_key_repository->delete();
		$this->activation_key_storage->delete();
		$this->activation_storage->delete();
		$this->permission_checker->apply();

		if ( ! $token ) {
			return;
		}

		$response = $this->api->dispatch(
			new API\Request\Deactivate( $token, $this->site_url )
		);

		$this->permission_checker
			->add_rule( new ApiDeactivateResponse( $response ) )
			->apply();

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		$this->products_updater->update( $token );

		$this->success_notice( $response->get( 'message' ) );
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Message::ERROR )->register();
	}

	private function success_notice( $message ) {
		( new Notice( $message ) )->register();
	}

}