<?php

namespace ACP\Access\Rule;

use ACP;
use ACP\Access\Permissions;
use ACP\Access\Rule;

class ApiDeactivateResponse implements Rule {

	/**
	 * @var ACP\API\Response
	 */
	protected $response;

	public function __construct( ACP\API\Response $response ) {
		$this->response = $response;
	}

	public function get_permissions() {
		return new Permissions( $this->response->get( 'permissions' ) ?: [] );
	}

}