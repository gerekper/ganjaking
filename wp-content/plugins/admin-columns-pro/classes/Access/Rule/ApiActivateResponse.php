<?php

namespace ACP\Access\Rule;

use ACP;
use ACP\Access\Permissions;
use ACP\Access\Rule;
use WP_Error;

class ApiActivateResponse implements Rule {

	/**
	 * @var ACP\API\Response
	 */
	protected $response;

	public function __construct( ACP\API\Response $response ) {
		$this->response = $response;
	}

	public function get_permissions() {
		$permissions = new Permissions( $this->response->get( 'permissions' ) ?: [] );

		if ( $this->response->has_error() ) {
			$data = $this->response->get( 'data' );

			if ( $data && is_array( $data['permissions'] ) ) {
				$permissions = new Permissions( $data['permissions'] );
			}
		}

		// `Usage` permissions are given when the API call fails.
		if ( $this->response->has_error() && $this->has_http_error_code( $this->response->get_error() ) ) {
			$permissions = $permissions->with_permission( Permissions::USAGE );
		}

		return $permissions;
	}

	/**
	 * @param WP_Error $error
	 *
	 * @return bool
	 * @see WP_Http
	 */
	private function has_http_error_code( WP_Error $error ): bool {
		$http_error_codes = [
			'http_failure', // no HTTP transports available
			'http_request_not_executed', // User has blocked requests through HTTP
			'http_request_failed', // any HTTP exceptions
		];

		return 0 !== count( array_intersect( $error->get_error_codes(), $http_error_codes ) );
	}

}