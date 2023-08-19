<?php

namespace ACP\Search\Comparison\Meta\DateTime;

use ACP\Search\Comparison\Meta;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Timestamp extends Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::TODAY,
			Operators::PAST,
			Operators::FUTURE,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		], false );

		parent::__construct( $operators, $meta_key, $type, Value::DATE, new Labels\Date() );
	}

	/**
	 * @param Value $value
	 * @param       $operator
	 *
	 * @return Value
	 */
	protected function map_value( Value $value, $operator ) {
		switch ( $operator ) {
			case Operators::EQ:
				$time = $this->get_timestamp_value( $value );

				return new Value(
					[
						$time,
						$time + DAY_IN_SECONDS - 1,
					],
					Value::INT
				);

			case Operators::GT_DAYS_AGO:
			case Operators::LT_DAYS_AGO:
			case Operators::WITHIN_DAYS:
				return new Value( $value->get_value(), Value::INT );

			default:
				return new Value( $this->get_timestamp_value( $value ), Value::INT );
		}
	}

	/**
	 * @param Value $value
	 *
	 * @return array|int
	 */
	private function get_timestamp_value( Value $value ) {
		return is_array( $value->get_value() )
			? array_map( [ $this, 'to_time' ], $value->get_value() )
			: $this->to_time( $value->get_value() );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$value = $this->map_value( $value, $operator );

		switch ( $operator ) {
			case Operators::EQ:
				$operator = Operators::BETWEEN;

		}

		return parent::get_meta_query(
			$operator,
			$value
		);
	}

	/**
	 * @param string $value
	 *
	 * @return int
	 */
	private function to_time( $value ) {
		return (int) strtotime( $value );
	}
}