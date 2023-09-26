<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP;
use ACP\Search\Value;

class Users extends User {

	use MultiMapTrait;

	protected function get_subquery( $operator, Value $value ) {
		$operator = $this->map_operator( $operator );
		$value = $this->map_value( $value, $operator );

		$where = ACP\Search\Helper\Sql\ComparisonFactory::create( $this->column, $operator, $value );

		return "SELECT ID FROM {$this->table} WHERE " . $where->prepare();
	}

}