<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Comparison;
use ACP;

class Text extends Comparison\Entry {

	public function __construct( $field ) {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::NEQ,
			ACP\Search\Operators::CONTAINS,
			ACP\Search\Operators::NOT_CONTAINS,
			ACP\Search\Operators::IS_EMPTY,
			ACP\Search\Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $field, $operators, ACP\Search\Value::STRING );
	}

}