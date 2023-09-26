<?php

namespace ACP\Editing\RequestHandler\Exception;

use RuntimeException;

class InvalidUserPermissionException extends RuntimeException {

	public function __construct() {
		parent::__construct( __( "You don't have permissions to edit this item", 'codepress-admin-columns' ) );
	}
}