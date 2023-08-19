<?php

namespace ACA\Types\Search;

use ACP\Search\Comparison;
use ACP\Search\Operators;

class File extends Comparison\Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

}