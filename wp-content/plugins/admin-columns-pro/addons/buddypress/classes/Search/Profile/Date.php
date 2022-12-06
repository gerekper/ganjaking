<?php

namespace ACA\BP\Search\Profile;

use ACA\BP\Helper\Select;
use ACA\BP\Search;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Date extends Search\Profile {

	public function __construct( $meta_key ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		], false );

		parent::__construct( $operators, $meta_key, Value::DATE, new Labels\Date() );
	}

	public function create_query_bindings( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			$value = new Value(
				[
					$value->get_value() . ' 00:00:00',
					$value->get_value() . ' 23:59:59',
				],
				Value::DATE
			);
			$operator = Operators::BETWEEN;
		}

		return parent::create_query_bindings( $operator, $value );
	}

}