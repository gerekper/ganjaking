<?php

namespace ACA\Types\Search;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkbox extends ACP\Search\Comparison\Meta
	implements Comparison\Values {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array(
			[
				1 => __( 'True', 'codepress-admin-columns' ),
				0 => __( 'False', 'codepress-admin-columns' ),
			]
		);
	}

	protected function get_meta_query( $operator, Value $value ) {
		$_operator = $value->get_value() === '0'
			? Operators::IS_EMPTY
			: Operators::NOT_IS_EMPTY;

		return parent::get_meta_query( $_operator, $value );
	}

}