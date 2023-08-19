<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;

trait OperatorMappingTrait {

	protected function map_operator( $operator ) {
		$mapping = [
			Operators::CONTAINS     => 'LIKE',
			Operators::NOT_CONTAINS => 'NOT LIKE',
		];

		return array_key_exists( $operator, $mapping )
			? $mapping[ $operator ]
			: $operator;
	}

}