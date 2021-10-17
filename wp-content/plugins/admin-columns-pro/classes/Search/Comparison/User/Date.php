<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Comparison;

abstract class Date extends Comparison\Date {

	/**
	 * @return string
	 */
	abstract protected function get_field();

	/**
	 * @return string
	 */
	protected function get_column() {
		global $wpdb;

		return sprintf( '%s.%s', $wpdb->users, $this->get_field() );
	}

}