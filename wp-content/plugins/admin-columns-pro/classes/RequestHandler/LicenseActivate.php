<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationUpdater;
use ACP\Access\PermissionChecker;
use ACP\Access\Rule\ApiActivateResponse;
use ACP\API;
use ACP\Nonce;
use ACP\RequestDispatcher;
use ACP\RequestHandler;
use ACP\Type\Activation\Key;
use ACP\Type\LicenseKey;
use ACP\Type\SiteUrl;
use ACP\Updates\PluginDataUpdater;
use InvalidArgumentException;

class LicenseActivate implements RequestHandler {

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
	 * @var PluginDataUpdater
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

	public function __construct( ActivationKeyStorage $activation_key_storage, RequestDispatcher $api, SiteUrl $site_url, PluginDataUpdater $products_updater, ActivationUpdater $activation_updater, PermissionChecker $permission_checker ) {
		$this->activation_key_storage = $activation_key_storage;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->products_updater = $products_updater;
		$this->activation_updater = $activation_updater;
		$this->permission_checker = $permission_checker;
	}

	/**
	 * @param Request $request
	 *
	 * @return void
	 */
	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\LicenseNonce() )->verify( $request ) ) {
			return;
		}

		$key = sanitize_text_field( $request->get( 'license' ) );

		if ( ! $key ) {
			$this->error_notice( __( 'Empty license key.', 'codepress-admin-columns' ) );

			return;
		}

		if ( ! LicenseKey::is_valid( $key ) ) {
			$this->error_notice( __( 'Invalid license key.', 'codepress-admin-columns' ) );

			return;
		}

		$license_key = new LicenseKey( $key );

		$response = $this->api->dispatch(
			new API\Request\Activate( $license_key, $this->site_url )
		);

		$this->permission_checker
			->add_rule( new ApiActivateResponse( $response ) )
			->apply();

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		try {
			$activation_key = new Key( $response->get( 'activation_key' ) );
		} catch ( InvalidArgumentException $e ) {
			$this->error_notice( $e->getMessage() );

			return;
		}

		$this->activation_key_storage->save( $activation_key );
		$this->activation_updater->update( $activation_key );
		$this->products_updater->update( $activation_key );

		wp_clean_plugins_cache();
		wp_update_plugins();

		( new Notice( $response->get( 'message' ) ) )->register();
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Message::ERROR )->register();
	}

}