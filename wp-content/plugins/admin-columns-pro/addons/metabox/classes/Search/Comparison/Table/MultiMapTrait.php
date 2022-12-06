<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP;
use ACP\Search\Value;

trait MultiMapTrait {

	protected function map_operator( $operator ) {
		switch ( $operator ) {
			case ACP\Search\Operators::EQ:
				return ACP\Search\Operators::CONTAINS;
			case ACP\Search\Operators::NEQ:
				return ACP\Search\Operators::NOT_CONTAINS;
			default:
				return $operator;
		}
	}

	protected function map_value( Value $value, $operator ) {
		if ( in_array( $operator, [ ACP\Search\Operators::CONTAINS, ACP\Search\Operators::NOT_CONTAINS ], true ) ) {
			$value = new Value(
				serialize( $value->get_value() ),
				$value->get_type()
			);
		}

		return $value;
	}

}