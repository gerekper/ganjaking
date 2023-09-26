<?php

namespace ACA\BP\Search\Profile;

use ACA\BP\Helper\Select;
use ACA\BP\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class Text extends Search\Profile {

	public function __construct( $meta_key ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		], false );

		parent::__construct( $operators, $meta_key, Value::STRING );
	}

}