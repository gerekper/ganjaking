<?php

namespace ACP\Search\Comparison;

use ACP\Search\Comparison;
use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;
use DateTime;

abstract class Date extends Comparison {

	/**
	 * DB column for SQL clause
	 * @return string
	 */
	abstract protected function get_column();

	public function __construct() {
		parent::__construct( $this->operators(), Value::DATE, new Labels\Date() );
	}

	/**
	 * @return Operators
	 */
	public function operators() {
		return new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			$value_factory = new DateValueFactory( $value->get_type() );
			$value = $value_factory->create_range_single_day( DateTime::createFromFormat( 'Y-m-d', $value->get_value() ) );

			$operator = Operators::BETWEEN;
		}

		$where = ComparisonFactory::create(
			$this->get_column(),
			$operator,
			$value
		)->prepare();

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

}