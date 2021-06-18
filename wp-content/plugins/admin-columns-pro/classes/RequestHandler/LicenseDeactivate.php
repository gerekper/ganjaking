<?php

namespace ACP\RequestHandler;

use AC\Message\Notice;
use ACP\API;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Plugins;
use ACP\RequestDispatcher;
use ACP\Type\SiteUrl;

class LicenseDeactivate {

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var Plugins
	 */
	private $plugins;

	public function __construct( LicenseKeyRepository $license_key_repository, LicenseRepository $license_repository, RequestDispatcher $api, SiteUrl $site_url, Plugins $plugins ) {
		$this->license_key_repository = $license_key_repository;
		$this->license_repository = $license_repository;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->plugins = $plugins;
	}

	public function handle( API\Request\Deactivation $request ) {
		$this->license_key_repository->delete();
		$this->license_repository->delete();

		$response = $this->api->dispatch( $request );

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		( new ProductsUpdate( $this->api ) )->handle(
			new API\Request\ProductsUpdate( $this->site_url, $this->plugins )
		);

		$this->success_notice( $response->get( 'message' ) );
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Notice::ERROR )->register();
	}

	private function success_notice( $message ) {
		( new Notice( $message ) )->register();
	}

}