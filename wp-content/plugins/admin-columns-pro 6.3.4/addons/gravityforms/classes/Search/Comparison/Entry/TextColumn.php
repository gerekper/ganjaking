<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class TextColumn extends ACP\Search\Comparison {

	/**
	 * @var string
	 */
	private $column;

	public function __construct( $column ) {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::CONTAINS,
			ACP\Search\Operators::NOT_CONTAINS,
		] );

		parent::__construct( $operators, ACP\Search\Value::STRING );

		$this->column = $column;
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$comparison = ACP\Search\Helper\Sql\ComparisonFactory::create( $this->column, $operator, $value );

		return ( new Bindings )->where( $comparison() );
	}

}