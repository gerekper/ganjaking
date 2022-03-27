<?php

namespace ACP\RequestHandler\Ajax;

use AC\Capabilities;
use AC\Nonce;
use AC\Request;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationUpdater;
use ACP\Access\PermissionChecker;
use ACP\Access\Rule\ApiActivateResponse;
use ACP\API;
use ACP\RequestAjaxHandler;
use ACP\RequestDispatcher;
use ACP\Type\Activation\Key;
use ACP\Type\LicenseKey;
use ACP\Type\SiteUrl;
use ACP\Updates\ProductsUpdater;
use InvalidArgumentException;

class LicenseActivate implements RequestAjaxHandler {

	/**
	 * @var ActivationKeyStorage
	 */
	private $activation_key_storage;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var ProductsUpdater
	 */
	private $products_updater;

	/**
	 * @var ActivationUpdater
	 */
	private $activation_updater;

	/**
	 * @var PermissionChecker
	 */
	private $permission_checker;

	public function __construct( ActivationKeyStorage $activation_key_storage, RequestDispatcher $api, SiteUrl $site_url, ProductsUpdater $products_updater, ActivationUpdater $activation_updater, PermissionChecker $permission_checker ) {
		$this->activation_key_storage = $activation_key_storage;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->products_updater = $products_updater;
		$this->activation_updater = $activation_updater;
		$this->permission_checker = $permission_checker;
	}

	public function handle() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error( __( 'Invalid request', 'codepress-admin-columns' ) );
		}

		$key = sanitize_text_field( $request->get( 'license' ) );

		if ( ! $key ) {
			$this->send_error_response( __( 'Empty license.', 'codepress-admin-columns' ) );
		}

		if ( ! LicenseKey::is_valid( $key ) ) {
			$this->send_error_response( __( 'Invalid license.', 'codepress-admin-columns' ) );
		}

		$license_key = new LicenseKey( $key );

		$response = $this->api->dispatch(
			new API\Request\Activate( $license_key, $this->site_url )
		);

		$this->permission_checker
			->add_rule( new ApiActivateResponse( $response ) )
			->apply();

		if ( $response->has_error() ) {
			$this->send_error_response( $response->get_error()->get_error_message(), $response->get( 'data' )['permissions'] );
		}

		try {
			$activation_key = new Key( $response->get( 'activation_key' ) );
		} catch ( InvalidArgumentException $e ) {
			$this->send_error_response( $response->get_error()->get_error_message() );
		}

		$this->activation_key_storage->save( $activation_key );
		$this->activation_updater->update( $activation_key );
		$this->products_updater->update( $activation_key );

		wp_send_json_success( [
			'permissions' => $response->get( 'permissions' ),
			'status'      => $response->get( 'status' ),
			'message'     => $response->get( 'message' ),
		] );
	}

	private function send_error_response( $message, $permissions = [] ) {
		wp_send_json_error( [
			'message'     => $message,
			'permissions' => $permissions,
		] );
	}

}