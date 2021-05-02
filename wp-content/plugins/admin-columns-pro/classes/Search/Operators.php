<?php

namespace ACP\Search;

use AC\Config;
use LogicException;

final class Operators extends Config {

	const EQ = '=';
	const NEQ = '!=';
	const GT = '>';
	const GTE = '>=';
	const LT = '<';
	const LTE = '<=';
	const CONTAINS = 'CONTAINS';
	const NOT_CONTAINS = 'NOT CONTAINS';
	const BEGINS_WITH = 'BEGINS WITH';
	const ENDS_WITH = 'ENDS WITH';
	const IN = 'IN';
	const NOT_IN = 'NOT IN';
	const BETWEEN = 'BETWEEN';
	const IS_EMPTY = 'IS EMPTY';
	const NOT_IS_EMPTY = 'NOT IS EMPTY';
	const TODAY = 'TODAY';
	const PAST = 'PAST';
	const FUTURE = 'FUTURE';
	const LT_DAYS_AGO = 'LT_DAYS_AGO';
	const GT_DAYS_AGO = 'GT_DAYS_AGO';
	const WITHIN_DAYS = 'WITHIN_DAYS';

	/**
	 * @param array $operators
	 * @param bool  $order
	 */
	public function __construct( array $operators, $order = true ) {
		if ( $order ) {
			$operators = array_intersect( $this->get_operators(), $operators );
		}

		parent::__construct( $operators );
	}

	/**
	 * @return array
	 */
	protected function get_operators() {
		return [
			self::EQ,
			self::NEQ,
			self::GT,
			self::GTE,
			self::LT,
			self::LTE,
			self::CONTAINS,
			self::NOT_CONTAINS,
			self::BEGINS_WITH,
			self::ENDS_WITH,
			self::IN,
			self::NOT_IN,
			self::BETWEEN,
			self::IS_EMPTY,
			self::NOT_IS_EMPTY,
			self::TODAY,
			self::PAST,
			self::FUTURE,
			self::LT_DAYS_AGO,
			self::GT_DAYS_AGO,
			self::WITHIN_DAYS,
		];
	}

	protected function validate_config() {
		$operators = $this->get_operators();

		foreach ( $this as $operator ) {
			if ( ! in_array( $operator, $operators ) ) {
				throw new LogicException( 'Invalid operator found.' );
			}
		}
	}

}