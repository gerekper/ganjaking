<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;

class Text extends Meta {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::EQ,
			Operators::NEQ,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		], false );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

}