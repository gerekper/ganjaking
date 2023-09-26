<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Comparison;
use ACP\Search\Operators;

abstract class Date extends Comparison\Date {

	/**
	 * @return string
	 */
	abstract protected function get_field();

	/**
	 * @return Operators
	 */
	public function operators() {
		return new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::TODAY,
			Operators::GT_DAYS_AGO,
			Operators::LT_DAYS_AGO,
		] );
	}

	/**
	 * @return string
	 */
	protected function get_column() {
		global $wpdb;

		return sprintf( '%s.%s', $wpdb->comments, $this->get_field() );
	}

}