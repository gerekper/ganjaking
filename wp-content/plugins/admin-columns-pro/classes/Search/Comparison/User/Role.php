<?php

namespace ACP\Search\Comparison\User;

use AC;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Role extends Meta
	implements Values {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

	public function get_values() {
		$options = [];

		foreach ( wp_roles()->roles as $key => $role ) {
			$options[ $key ] = translate_user_role( $role['name'] );
		}

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}