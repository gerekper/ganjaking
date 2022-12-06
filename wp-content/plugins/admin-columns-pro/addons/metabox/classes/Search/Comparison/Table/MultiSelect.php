<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP;
use ACP\Search\Value;

class MultiSelect extends Select {

	use MultiMapTrait;

	protected function get_subquery( $operator, Value $value ) {

		$_operator = $this->map_operator( $operator );
		$_value = $this->map_value( $value, $operator );

		$where = ACP\Search\Helper\Sql\ComparisonFactory::create( $this->column, $_operator, $_value );

		return "SELECT ID FROM {$this->table} WHERE " . $where->prepare();
	}

}