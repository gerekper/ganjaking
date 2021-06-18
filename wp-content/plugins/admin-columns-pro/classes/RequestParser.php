<?php

namespace ACP;

use AC\Capabilities;
use AC\Registrable;
use AC\Request;
use ACP\RequestHandler\LicenseActivate;
use ACP\RequestHandler\LicenseDeactivate;
use ACP\RequestHandler\LicenseUpdate;

class RequestParser implements Registrable {

	const NONCE_ACTION = 'acp-license';
	const ACTION_ACTIVATE = 'activate';
	const ACTION_DEACTIVATE = 'deactivate';
	const ACTION_UPDATE = 'update';

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var Type\SiteUrl
	 */
	private $site_url;

	/**
	 * @var Plugins
	 */
	private $plugins;

	public function __construct(
		RequestDispatcher $api,
		LicenseRepository $license_repository,
		LicenseKeyRepository $license_key_repository,
		Type\SiteUrl $site_url,
		Plugins $plugins
	) {
		$this->api = $api;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
		$this->plugins = $plugins;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	public function handle_request() {
		$request = new Request();

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_acnonce' ), self::NONCE_ACTION ) ) {
			return;
		}

		switch ( $request->get( 'action' ) ) {
			case self::ACTION_UPDATE:
				$key = $this->license_key_repository->find();

				if ( ! $key ) {
					return;
				}

				$request_handler = new LicenseUpdate(
					$this->license_repository,
					$this->api
				);

				$request_handler->handle(
					new API\Request\SubscriptionDetails( $key, $this->site_url )
				);

				return;
			case self::ACTION_ACTIVATE:
				$request_handler = new LicenseActivate(
					$this->license_key_repository,
					$this->license_repository,
					$this->api,
					$this->site_url,
					$this->plugins
				);

				$request_handler->handle( $request );

				return;
			case self::ACTION_DEACTIVATE:
				$key = $this->license_key_repository->find();

				if ( ! $key ) {
					return;
				}

				$request_handler = new LicenseDeactivate(
					$this->license_key_repository,
					$this->license_repository,
					$this->api,
					$this->site_url,
					$this->plugins
				);

				$request_handler->handle( new API\Request\Deactivation( $key, $this->site_url ) );

				return;
		}
	}

}