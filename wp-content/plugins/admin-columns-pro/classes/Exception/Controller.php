<?php

namespace ACP\Exception;

use LogicException;

class Controller extends LogicException {

	/**
	 * @param string $action
	 *
	 * @return Controller
	 */
	public static function from_invalid_action( $action ) {
		return new self( sprintf( 'The action %s is not defined.', $action ) );
	}

}