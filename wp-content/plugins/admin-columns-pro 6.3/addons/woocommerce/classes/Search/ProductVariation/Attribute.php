<?php

namespace ACA\WC\Search\ProductVariation;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Attribute extends Comparison\Meta {

	public function __construct( $meta_key ) {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, AC\MetaType::POST );
	}

}