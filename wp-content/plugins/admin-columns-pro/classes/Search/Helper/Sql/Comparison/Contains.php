<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Value;

class Contains extends Like {

	public function bind_value( Value $value ) {
		$value = new Value(
			$this->value_contains( $value->get_value() ),
			$value->get_type()
		);

		return parent::bind_value( $value );
	}

}