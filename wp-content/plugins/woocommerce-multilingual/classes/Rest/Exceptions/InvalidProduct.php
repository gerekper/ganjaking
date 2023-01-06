<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidProduct extends WC_REST_Exception {

	/**
	 * @param int $product_id
	 */
	public function __construct( $product_id ) {
		parent::__construct(
			422,
			/* translators: $s is a product ID */
			sprintf( __( 'Product not found: %d', 'woocommerce-multilingual' ),
				$product_id ),
			422
		);
	}

}