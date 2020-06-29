<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

abstract class PostField extends Comparison {

	/**
	 * @return string
	 */
	abstract protected function get_field();

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$where = ComparisonFactory::create(
			$wpdb->posts . '.' . $this->get_field(),
			$operator,
			$value
		)->prepare();

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

}