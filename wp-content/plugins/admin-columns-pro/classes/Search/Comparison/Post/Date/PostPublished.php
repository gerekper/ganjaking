<?php

namespace ACP\Search\Comparison\Post\Date;

use ACP\Search\Value;

class PostPublished extends PostDate {

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = parent::create_query_bindings( $operator, $value );
		$bindings->where( $bindings->get_where() . ' AND ' . $wpdb->posts . ".post_status = 'publish'" );

		return $bindings;
	}

}