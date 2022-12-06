<?php

namespace ACA\EC\Search\Event;

use AC;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class Featured extends Meta
	implements Values {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	protected function get_meta_query( $operator, Value $value ) {
		if ( '0' === $value->get_value() ) {
			$operator = Operators::IS_EMPTY;
		}

		return parent::get_meta_query( $operator, $value );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			__( 'False', 'codepress-admin-columns' ),
			__( 'True', 'codepress-admin-columns' ),
		] );
	}

}