<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Comparison;

abstract class Date extends Comparison\Date {

	abstract protected function get_field(): string;

	protected function get_column(): string {
		global $wpdb;

		return sprintf( '%s.%s', $wpdb->posts, $this->get_field() );
	}

}