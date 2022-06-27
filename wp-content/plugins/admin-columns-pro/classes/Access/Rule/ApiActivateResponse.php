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
		if ( $this->response->has_error() && $this->has_error_code( $this->response->get_error(), 'http_request_failed' ) ) {
			$permissions = $permissions->with_permission( Permissions::USAGE );
		}

		return $permissions;
	}

	private function has_error_code( WP_Error $error, $code ) {
		return in_array( $code, $error->get_error_codes(), true );
	}

}