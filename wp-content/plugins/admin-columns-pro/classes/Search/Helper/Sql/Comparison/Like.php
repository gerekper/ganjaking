<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Value;

class Like extends Comparison
	implements Negatable {

	/**
	 * @param string $column
	 * @param Value  $value
	 */
	public function __construct( $column, Value $value ) {
		$operator = 'LIKE';

		if ( $this->is_negated() ) {
			$operator = 'NOT ' . $operator;
		}

		$value = new Value(
			$this->escape_value( $value->get_value() ),
			$value->get_type()
		);

		parent::__construct( $column, $operator, $value );
	}

	public function is_negated() {
		return false;
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function escape_value( $value ) {
		global $wpdb;

		return $wpdb->esc_like( $value );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function value_begins_with( $value ) {
		return $value . '%';
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function value_ends_with( $value ) {
		return '%' . $value;
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function value_contains( $value ) {
		return '%' . $value . '%';
	}

}