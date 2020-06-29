<?php

namespace ACP\Exception;

use LogicException;

class ControllerException extends LogicException {

	/**
	 * @param string $action
	 *
	 * @return self
	 */
	public static function from_invalid_action( $action ) {
		return new self( sprintf( 'The action %s is not defined.', $action ) );
	}

}