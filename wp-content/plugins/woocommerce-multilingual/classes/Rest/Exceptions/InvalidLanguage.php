<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidLanguage extends WC_REST_Exception {

	/**
	 * @param string $language_code
	 */
	public function __construct( $language_code ) {
		parent::__construct(
			422,
			/* translators: $s is a language code */
			sprintf( __( 'Invalid language parameter: "%s"', 'woocommerce-multilingual' ),
				$language_code ),
			422
		);
	}

}