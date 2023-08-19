<?php

namespace ACP\Search\Comparison\User;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

abstract class UserField extends Comparison {

	public function get_meta_type() {
		return MetaType::USER;
	}

	/**
	 * @return string
	 */
	abstract protected function get_field();

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$where = ComparisonFactory::create(
			$wpdb->users . '.' . $this->get_field(),
			$operator,
			$value
		)->prepare();

		$bindings = new Bindings();

		return $bindings->where( $where );
	}

}