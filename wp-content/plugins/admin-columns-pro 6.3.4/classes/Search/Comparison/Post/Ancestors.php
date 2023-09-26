<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Ancestors extends Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, Value::INT );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$operator = $operator === Operators::IS_EMPTY
			? Operators::EQ
			: Operators::NEQ;

		$new_value = new Value(
			0,
			Value::INT
		);

		$where = ComparisonFactory::create(
			$wpdb->posts . '.post_parent',
			$operator,
			$new_value
		)->prepare();

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

}