<?php

namespace ACP\Search\Middleware\Mapping;

use ACP\Search\Middleware\Mapping;
use ACP\Search\Value;

class ValueType extends Mapping {

	protected function get_properties() {
		return [
			Value::STRING  => 'string',
			Value::INT     => 'integer',
			Value::DATE    => 'date',
			Value::DECIMAL => 'double',
		];
	}

}