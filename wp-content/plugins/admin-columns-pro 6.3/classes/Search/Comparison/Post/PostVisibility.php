<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class PostVisibility extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;
		$binding = new Bindings();

		switch ( $value->get_value() ) {
			case 'private':
				$value = new Value( 'private', Value::STRING );

				$binding->where(
					$this->create_where( 'post_status', $operator, $value )
				);

				break;
			case 'protected':
				$value = new Value( null, Value::STRING );

				$binding->where(
					$this->create_where( 'post_password', Operators::NOT_IS_EMPTY, $value )
				);

				break;
			case 'public':
				$binding->where( "(
					{$wpdb->posts}.post_password = '' AND
					{$wpdb->posts}.post_status != 'private'
				)" );

				break;
		}

		return $binding;
	}

	private function create_where( $field, $operator, $value ) {
		global $wpdb;

		return ComparisonFactory::create(
			$wpdb->posts . '.' . $field,
			$operator,
			$value
		)->prepare();
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'private'   => _x( 'Private', 'post status' ),
			'protected' => _x( 'Password protected', 'post status' ),
			'public'    => __( 'Public' ),
		] );
	}

}