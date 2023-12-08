<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;

class EmptyNotEmpty extends Meta {

	public function __construct( string $meta_key ) {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key );
	}

}