<?php

namespace ACP\Search\Comparison\Media;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class MetaData extends Comparison\Meta {

	/**
	 * @var string
	 */
	private $sub_key;

	public function __construct( $sub_key ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::CONTAINS,
		] );

		$this->sub_key = (string) $sub_key;

		parent::__construct( $operators, '_wp_attachment_metadata' );
	}

	protected function get_meta_query( string $operator, Value $value ): array {
		if ( Operators::EQ === $operator ) {
			$operator = Operators::CONTAINS;
			$value = new Value(
				sprintf( '"%s";%s', $this->sub_key, serialize( $value->get_value() ) ),
				$value->get_type()
			);
		}

		return parent::get_meta_query( $operator, $value );
	}

}