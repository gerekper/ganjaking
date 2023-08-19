<?php

namespace ACA\WC\Search\ShopCoupon;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class EmailRestriction extends Comparison\Meta {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, 'customer_email', MetaType::POST );
	}

}