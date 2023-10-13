<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class MissingLanguage extends WC_REST_Exception {

	public function __construct() {
		parent::__construct(
			422,
			__( 'Using "translation_of" requires providing a "lang" parameter too', 'woocommerce-multilingual' ),
			422
		);
	}

}