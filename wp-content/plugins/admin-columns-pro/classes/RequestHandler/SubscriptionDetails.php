<?php

namespace ACP\RequestHandler;

use ACP\API;
use ACP\Entity;
use ACP\LicenseRepository;
use ACP\RequestDispatcher;
use ACP\Type\License\ExpiryDate;
use ACP\Type\License\Key;
use ACP\Type\License\RenewalDiscount;
use ACP\Type\License\RenewalMethod;
use ACP\Type\License\Status;
use DateTime;
use DateTimeZone;
use WP_Error;

class SubscriptionDetails {

	const LICENSE_NOT_FOUND_ERROR_CODE = 'license_not_found';
	const SITE_NOT_REGISTERED_ERROR_CODE = 'activation_not_registered';

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	public function __construct( LicenseRepository $license_repository, RequestDispatcher $api ) {
		$this->license_repository = $license_repository;
		$this->api = $api;
	}

	/**
	 * @param API\Request\SubscriptionDetails $request
	 *
	 * @return API\Response
	 */
	public function handle( API\Request\SubscriptionDetails $request ) {
		$response = $this->api->dispatch( $request );

		if ( $response->has_error() ) {

			// Remove license info when their subscription has not been found or the site is not registered.
			if (
				$this->has_error_code( $response->get_error(), self::LICENSE_NOT_FOUND_ERROR_CODE ) ||
				$this->has_error_code( $response->get_error(), self::SITE_NOT_REGISTERED_ERROR_CODE )
			) {
				$this->license_repository->delete();
			}
		} else {

			$key = new Key( $request->get_body()['subscription_key'] );

			$license = $this->create_license_from_response( $key, $response );

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