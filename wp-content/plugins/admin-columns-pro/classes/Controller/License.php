<?php

namespace ACP\Controller;

use AC\Capabilities;
use AC\Message\Notice;
use AC\Registrable;
use AC\Storage;
use ACP\API;
use ACP\API\Cached;
use ACP\Entity;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Plugins;
use ACP\RequestDispatcher;
use ACP\Type;
use ACP\Type\License\ExpiryDate;
use ACP\Type\License\Key;
use ACP\Type\License\RenewalDiscount;
use ACP\Type\License\RenewalMethod;
use ACP\Type\License\Status;
use DateTime;
use DateTimeZone;
use LogicException;
use WP_Error;

class License implements Registrable {

	const NONCE_ACTION = 'acp-license';
	const PERIODIC_CHECK_TRANSIENT_KEY = 'acp_periodic_license_check';
	const ACTIVATE_ACTION = 'activate';
	const DEACTIVATE_ACTION = 'deactivate';
	const UPDATE_ACTION = 'update';
	const LICENSE_NOT_FOUND_ERROR_CODE = 'license_not_found';
	const SITE_NOT_REGISTERED_ERROR_CODE = 'activation_not_registered';

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
		add_action( 'shutdown', [ $this, 'handle_daily_update_subscription_details' ] );
	}

	public function handle_request() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_acnonce' ), self::NONCE_ACTION ) ) {
			return;
		}

		switch ( filter_input( INPUT_POST, 'action' ) ) {
			case self::UPDATE_ACTION:
				$this->handle_update_request();
				break;
			case self::ACTIVATE_ACTION:
				$this->handle_activate_request();
				break;
			case self::DEACTIVATE_ACTION:
				$this->handle_deactivate_request();
				break;
		}
	}

	public function handle_daily_update_subscription_details() {
		$cache = new Storage\Timestamp(
			new Storage\Option( self::PERIODIC_CHECK_TRANSIENT_KEY )
		);

		if ( ! $cache->is_expired() ) {
			return;
		}

		$this->update_subscription_details();

		$cache->save( time() + DAY_IN_SECONDS );
	}

	private function handle_update_request() {
		$response = $this->update_subscription_details();

		if ( $response && $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		$this->success_notice( __( 'License information has been updated.', 'codepress-admin-columns' ) );
	}

	/**
	 * @return void
	 */
	private function force_plugin_update_check() {
		$api = new API\Cached( $this->api );
		$api->dispatch( new API\Request\ProductsUpdate( $this->site_url, $this->plugins, $this->license_key_repository->find() ), [
			Cached::FORCE_UPDATE => true,
		] );

		wp_clean_plugins_cache();
	}

	private function handle_activate_request() {
		$key = sanitize_text_field( filter_input( INPUT_POST, 'license' ) );

		if ( empty( $key ) ) {
			$this->license_key_repository->delete();

			$this->error_notice( __( 'Empty license.', 'codepress-admin-columns' ) );

			return;
		}

		try {
			$license_key = new Key( $key );
		} catch ( LogicException $e ) {
			$this->error_notice( $e->getMessage() );

			return;
		}

		$this->license_key_repository->save( $license_key );

		$response = $this->api->dispatch( new API\Request\Activation( $license_key, $this->site_url ) );

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		$this->success_notice( $response->get( 'message' ) );

		$this->force_plugin_update_check();

		$this->update_subscription_details();
	}

	private function handle_deactivate_request() {
		$license_key = $this->license_key_repository->find();

		$this->license_key_repository->delete();
		$this->license_repository->delete();

		if ( ! $license_key ) {
			return;
		}

		$response = $this->api->dispatch( new API\Request\Deactivation( $license_key, $this->site_url ) );

		if ( $response->has_error() ) {
			$this->error_notice( $response->get_error()->get_error_message() );

			return;
		}

		$this->force_plugin_update_check();

		$this->success_notice( $response->get( 'message' ) );
	}

	/**
	 * @param WP_Error $error
	 * @param string   $code
	 *
	 * @return bool
	 */
	private function has_error_code( WP_Error $error, $code ) {
		return in_array( $code, $error->get_error_codes(), true );
	}

	/**
	 * @return API\Response|null
	 */
	private function update_subscription_details() {
		$license_key = $this->license_key_repository->find();

		if ( ! $license_key ) {
			return null;
		}

		$response = $this->api->dispatch( new API\Request\SubscriptionDetails( $license_key, $this->site_url ) );

		if ( $response->has_error() ) {

			// Remove license info when their subscription has not been found or the site is not registered.
			if (
				$this->has_error_code( $response->get_error(), self::LICENSE_NOT_FOUND_ERROR_CODE ) ||
				$this->has_error_code( $response->get_error(), self::SITE_NOT_REGISTERED_ERROR_CODE )
			) {
				$this->license_repository->delete();
			}
		} else {

			$license = $this->create_license_from_response( $license_key, $response );

			if ( $license ) {
				$this->license_repository->save( $license );
			}
		}

		return $response;
	}

	/**
	 * @param Key          $license_key
	 * @param API\Response $response
	 *
	 * @return Entity\License|null
	 */
	private function create_license_from_response( Key $license_key, API\Response $response ) {
		$expiry_date = $response->get( 'expiry_date' )
			? DateTime::createFromFormat( 'Y-m-d H:i:s', $response->get( 'expiry_date' ), new DateTimeZone( 'Europe/Amsterdam' ) )
			: null;

		if ( $expiry_date === false ) {
			return null;
		}

		$status = $response->get( 'status' );

		if ( ! Status::is_valid( $status ) ) {
			return null;
		}

		$method = $response->get( 'renewal_method' );

		if ( ! RenewalMethod::is_valid( $method ) ) {
			return null;
		}

		$discount = $response->get( 'renewal_discount' );

		if ( ! RenewalDiscount::is_valid( $discount ) ) {
			$discount = 0;
		}

		return new Entity\License(
			$license_key,
			new Status( $status ),
			new RenewalDiscount( $discount ),
			new RenewalMethod( $method ),
			new ExpiryDate( $expiry_date )
		);
	}

	private function error_notice( $message ) {
		( new Notice( $message ) )->set_type( Notice::ERROR )->register();
	}

	private function success_notice( $message ) {
		( new Notice( $message ) )->register();
	}

}