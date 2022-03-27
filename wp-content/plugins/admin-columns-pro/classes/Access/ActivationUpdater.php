<?php

namespace ACP\Access;

use ACP\Access\Rule\ApiDetailsResponse;
use ACP\API;
use ACP\Entity;
use ACP\LicenseKeyRepository;
use ACP\PluginRepository;
use ACP\RequestDispatcher;
use ACP\Type\Activation\ExpiryDate;
use ACP\Type\Activation\Key;
use ACP\Type\Activation\Products;
use ACP\Type\Activation\RenewalMethod;
use ACP\Type\Activation\Status;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use WP_Error;

class ActivationUpdater {

	/**
	 * @var ActivationKeyStorage
	 */
	private $activation_key_storage;

	/**
	 * @var ActivationStorage
	 */
	private $activation_storage;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var PluginRepository
	 */
	private $plugin_repository;

	/**
	 * @var PermissionChecker
	 */
	private $permission_checker;

	public function __construct( ActivationKeyStorage $activation_key_storage, ActivationStorage $activation_storage, LicenseKeyRepository $license_key_repository, RequestDispatcher $api, SiteUrl $site_url, PluginRepository $plugin_repository, PermissionChecker $permission_checker ) {
		$this->activation_key_storage = $activation_key_storage;
		$this->activation_storage = $activation_storage;
		$this->license_key_repository = $license_key_repository;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->plugin_repository = $plugin_repository;
		$this->permission_checker = $permission_checker;
	}

	/**
	 * @return API\Response
	 */
	public function update( ActivationToken $token ) {
		$request = new API\Request\SubscriptionDetails(
			$this->site_url,
			$this->plugin_repository->find_all(),
			$token
		);

		$api_response = $this->api->dispatch( $request );

		if ( $api_response->has_error() ) {
			// Remove license info when their subscription has not been found or the site is not registered.
			if (
				$this->has_error_code( $api_response->get_error(), 'license_not_found' ) ||
				$this->has_error_code( $api_response->get_error(), 'activation_not_registered' )
			) {
				$this->activation_key_storage->delete();
				$this->activation_storage->delete();
				$this->license_key_repository->delete();
				$this->permission_checker->apply();
			}

			return $api_response;
		}

		$this->permission_checker
			->add_rule( new ApiDetailsResponse( $api_response ) )
			->apply();

		$activation = $this->create_activation_from_response( $api_response );
		$activation_key = $this->create_activation_key_from_response( $api_response );

		if ( $activation_key && $activation ) {
			$this->activation_key_storage->save( $activation_key );
			$this->activation_storage->save( $activation_key, $activation );

			// old key is no longer needed since 5.7
			if ( 'subscription_key' === $token->get_type() ) {
				$this->license_key_repository->delete();
			}
		}

		return $api_response;
	}

	private function create_activation_key_from_response( API\Response $api_response ) {
		try {
			$key = new Key( $api_response->get( 'activation_key' ) );
		} catch ( InvalidArgumentException $e ) {
			return null;
		}

		return $key;
	}

	private function create_activation_from_response( API\Response $api_response ) {
		$expiry_date = $api_response->get( 'expiry_date' )
			? DateTime::createFromFormat( 'Y-m-d H:i:s', $api_response->get( 'expiry_date' ), new DateTimeZone( 'Europe/Amsterdam' ) )
			: null;

		if ( $expiry_date === false ) {
			return null;
		}

		$status = $api_response->get( 'status' );

		if ( ! Status::is_valid( $status ) ) {
			return null;
		}

		$method = $api_response->get( 'renewal_method' );

		if ( ! RenewalMethod::is_valid( $method ) ) {
			return null;
		}

		$products = $api_response->get( 'products' ) ?: [];

		return new Entity\Activation(
			new Status( $status ),
			new RenewalMethod( $method ),
			new ExpiryDate( $expiry_date ),
			new Products( $products )
		);
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

}