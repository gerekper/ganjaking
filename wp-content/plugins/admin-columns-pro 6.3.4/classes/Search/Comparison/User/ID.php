<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Operators;
use ACP\Search\Value;

class ID extends UserField {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $operators, Value::INT );
	}

	protected function get_field() {
		return 'ID';
	}

}