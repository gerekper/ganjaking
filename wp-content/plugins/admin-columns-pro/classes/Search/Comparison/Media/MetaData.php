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

		$this->sub_key = $sub_key;

		parent::__construct( $operators, '_wp_attachment_metadata', 'post' );
	}

	protected function get_meta_query( $operator, Value $value ) {
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