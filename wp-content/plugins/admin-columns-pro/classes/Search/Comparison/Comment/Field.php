<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

abstract class Field extends Comparison {

	/**
	 * @return string
	 */
	abstract protected function get_field();

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$where = ComparisonFactory::create(
			$wpdb->comments . '.' . $this->get_field(),
			$operator,
			$value
		)->prepare();

		$bindings = new Bindings();

		return $bindings->where( $where );
	}

}