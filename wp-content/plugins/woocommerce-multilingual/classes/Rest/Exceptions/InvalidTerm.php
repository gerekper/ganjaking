<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidTerm extends WC_REST_Exception {

	/**
	 * @param int $term_id
	 */
	public function __construct( $term_id ) {
		parent::__construct(
			422,
			/* translators: $s is a term ID */
			sprintf( __( 'Term not found: %d', 'woocommerce-multilingual' ),
				$term_id ),
			422
		);
	}

}