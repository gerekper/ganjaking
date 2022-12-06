<?php

namespace ACA\WC\Search\Product;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ReviewsEnabled extends Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$value = new Value(
			( Operators::IS_EMPTY === $operator ) ? 'closed' : 'open',
			$value->get_type()
		);

		$where = ComparisonFactory::create(
			"{$wpdb->posts}.comment_status",
			$operator,
			$value
		);

		$bindings = new Bindings();
		$bindings->where( $where() );

		return $bindings;
	}

}