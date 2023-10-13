<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class Generic extends WC_REST_Exception {

	/**
	 * @param string $text
	 */
	public function __construct( $text ) {
		parent::__construct( 422, $text, 422 );
	}

}