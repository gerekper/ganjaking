<?php

namespace ACP\Search\Comparison\User;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class TrueFalse extends Comparison\Meta {

	public function __construct( $meta_key ) {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, MetaType::USER );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$value = new Value(
			( $operator === Operators::IS_EMPTY ) ? 'false' : 'true',
			$value->get_type()
		);

		return parent::get_meta_query( Operators::EQ, $value );
	}

}