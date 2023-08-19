<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Toggle extends Comparison\Repeater {

	public function __construct( $meta_type, $parent_key, $sub_key ) {
		$operators = new Operators( [
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		return parent::create_query_bindings(
			$operator === Operators::NOT_IS_EMPTY ? Operators::EQ : $operator,
			new Value(
				1,
				Value::INT
			)
		);
	}

}