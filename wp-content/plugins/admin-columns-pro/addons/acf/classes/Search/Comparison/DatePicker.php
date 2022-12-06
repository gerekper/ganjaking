<?php

namespace ACA\ACF\Search\Comparison;

use ACP;
use ACP\Search\Comparison\Meta;

class DatePicker extends Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::GT,
			ACP\Search\Operators::LT,
			ACP\Search\Operators::BETWEEN,
			ACP\Search\Operators::FUTURE,
			ACP\Search\Operators::PAST,
			ACP\Search\Operators::LT_DAYS_AGO,
			ACP\Search\Operators::GT_DAYS_AGO,
			ACP\Search\Operators::WITHIN_DAYS,
			ACP\Search\Operators::PAST,
			ACP\Search\Operators::TODAY,
			ACP\Search\Operators::IS_EMPTY,
			ACP\Search\Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type, ACP\Search\Value::DATE, new ACP\Search\Labels\Date() );
	}

	private function map_value( ACP\Search\Value $value, $operator ) {
		switch ( $operator ) {
			case ACP\Search\Operators::GT_DAYS_AGO:
			case ACP\Search\Operators::LT_DAYS_AGO:
			case ACP\Search\Operators::WITHIN_DAYS:
			case ACP\Search\Operators::TODAY:
				return $value;
			default:
				return new ACP\Search\Value(
					$this->format_date( $value->get_value() ),
					ACP\Search\Value::INT
				);
		}
	}

	protected function get_meta_query( $operator, ACP\Search\Value $value ) {
		$value = $this->map_value( $value, $operator );

		if ( in_array( $operator, [ ACP\Search\Operators::FUTURE, ACP\Search\Operators::PAST ] ) ) {
			$compare = ACP\Search\Operators::FUTURE === $operator ? '>' : '<';

			return [
				'key'     => $this->get_meta_key(),
				'compare' => $compare,
				'type'    => 'NUMERIC',
				'value'   => date( 'Ymd' ),
			];
		}

		return parent::get_meta_query( $operator, $value );
	}

	/**
	 * @param array|string $value
	 *
	 * @return array|string
	 */
	private function format_date( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = $this->format_date( $item );
			}
		} else {
			$value = date( 'Ymd', strtotime( $value ) );
		}

		return $value;
	}

}