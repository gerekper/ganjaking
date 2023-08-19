<?php

namespace ACA\BP\Search\User;

use ACA\BP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Date extends Comparison\Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type, Value::DATE );
	}

	protected function get_meta_query( $operator, Value $value ) {
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

		return parent::get_meta_query( $operator, $value );
	}

}