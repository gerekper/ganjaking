<?php

namespace ACA\WC\Sorting\ShopCoupon;

use ACP;
use ACP\Sorting\Type\DataType;

class ExpiryDate extends ACP\Sorting\Model\Post\Meta {

	public function __construct( $meta_key ) {
		parent::__construct( $meta_key, new DataType( DataType::NUMERIC ) );
	}

}