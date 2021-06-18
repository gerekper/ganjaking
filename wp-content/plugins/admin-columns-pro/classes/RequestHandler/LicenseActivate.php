<?php

namespace ACP\RequestHandler;

use AC\Message\Notice;
use AC\Request;
use ACP\API;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Plugins;
use ACP\RequestDispatcher;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

class LicenseActivate {

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

	/**
	 * @param Request $request
	 *
	 * @return void
	 */
	public function handle( Request $request ) {
		$key = sanitize_text_field( $request->get( 'license' ) );

		if ( ! $key ) {
			$this->license_key_repository->delete();

			$this->error_notice( __( 'Empty license.', 'codepress-admin-columns' ) );

			return;
		}

		if ( ! Key::is_valid( $key ) ) {
			$this->license_key_repository->delete();

			$this->error_notice( __( 'Invalid license key.', 'codepress-admin-columns' ) );

			return;
		}

		$license_key = new Key( $key );

		$this->license_key_repository->save( $license_key );

		$response = $this->api->dispatch(
			new API\Request\Activation( $license_key, $this->site_url )
		);

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		( new ProductsUpdate( $this->api ) )->handle(
			new API\Request\ProductsUpdate( $this->site_url, $this->plugins, $license_key )
		);

		( new SubscriptionDetails( $this->license_repository, $this->api ) )->handle(
			new API\Request\SubscriptionDetails( $license_key, $this->site_url )
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