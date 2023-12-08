<?php

namespace ACA\Types\Search;

use AC;
use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkbox extends ACP\Search\Comparison\Meta
	implements Comparison\Values {

	public function __construct( string $meta_key, string $value_type = null ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $value_type );
	}

	public function get_values(): Options {
		return AC\Helper\Select\Options::create_from_array(
			[
				1 => __( 'True', 'codepress-admin-columns' ),
				0 => __( 'False', 'codepress-admin-columns' ),
			]
		);
	}

	protected function get_meta_query( string $operator, Value $value ): array {
		if( $operator === Operators::EQ ){
			$operator = $value->get_value() === '0'
				? Operators::IS_EMPTY
				: Operators::NOT_IS_EMPTY;
			$value = new Value( null );
		}

		return parent::get_meta_query( $operator, $value );
	}

}