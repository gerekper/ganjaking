<?php

namespace ACA\ACF\Search\Comparison;

use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;

class Link extends Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

}