<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;
use ACP\Search\Value;

class HasTerm extends Taxonomy {

	/**
	 * @var int
	 */
	protected $term_id;

	public function __construct( $taxonomy, $term_id ) {
		$this->term_id = $term_id;

		parent::__construct( $taxonomy );
	}

	public function get_operators() {
		return new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$value = new Value(
			$this->term_id,
			$value->get_type()
		);

		return parent::create_query_bindings(
			Operators::IS_EMPTY === $operator ? Operators::NEQ : Operators::EQ,
			new Value(
				$this->term_id,
				$value->get_type()
			)
		);
	}

}