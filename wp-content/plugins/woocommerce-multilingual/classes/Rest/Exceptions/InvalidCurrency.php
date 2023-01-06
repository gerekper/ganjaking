<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidCurrency extends WC_REST_Exception {

	/**
	 * @param string $currency_code
	 */
	public function __construct( $currency_code ) {
		parent::__construct(
			422,
			/* translators: $s is a currency code */
			sprintf( __( 'Invalid currency parameter: "%s"', 'woocommerce-multilingual' ),
				$currency_code ),
			422
		);
	}
}