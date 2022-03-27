<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Capabilities;
use AC\Nonce;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationStorage;
use ACP\Access\ActivationUpdater;
use ACP\Access\PermissionChecker;
use ACP\ActivationTokenFactory;
use ACP\LicenseKeyRepository;
use ACP\PluginRepository;
use ACP\RequestAjaxHandler;
use ACP\RequestDispatcher;
use ACP\Type\SiteUrl;

class SubscriptionUpdate implements RequestAjaxHandler {

	/**
	 * @var ActivationStorage
	 */
	private $activation_storage;

	/**
	 * @var ActivationKeyStorage
	 */
	private $activation_key_storage;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var PermissionChecker
	 */
	private $permission_checker;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $activation_url;

	/**
	 * @var ActivationTokenFactory
	 */
	private $token_factory;

	/**
	 * @var PluginRepository
	 */
	private $plugin_repository;

	public function __construct( ActivationStorage $activation_storage, ActivationKeyStorage $activation_key_storage, LicenseKeyRepository $license_key_repository, PermissionChecker $permission_checker, RequestDispatcher $api, SiteUrl $activation_url, ActivationTokenFactory $token_factory, PluginRepository $plugin_repository ) {
		$this->activation_storage = $activation_storage;
		$this->activation_key_storage = $activation_key_storage;
		$this->license_key_repository = $license_key_repository;
		$this->permission_checker = $permission_checker;
		$this->api = $api;
		$this->activation_url = $activation_url;
		$this->token_factory = $token_factory;
		$this->plugin_repository = $plugin_repository;
	}

	public function handle() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$request = new AC\Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$activation_token = $this->token_factory->create();

		if ( ! $activation_token ) {
			wp_send_json_error();
		}

		$updater = new ActivationUpdater(
			$this->activation_key_storage,
			$this->activation_storage,
			$this->license_key_repository,
			$this->api,
			$this->activation_url,
			$this->plugin_repository,
			$this->permission_checker
		);

		$api_response = $updater->update( $activation_token );

		if ( $api_response->has_error() ) {
			wp_send_json_error( $api_response->get_error()->get_error_message() );
		}

		wp_send_json_success();
	}

}