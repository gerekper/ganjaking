<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class AuthorRole extends Comparison implements Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$user_ids = get_users( [
			'fields' => 'ids',
			'role'   => $value->get_value(),
		] );

		$where = ComparisonFactory::create(
			$wpdb->posts . '.post_author',
			Operators::IN,
			new Value( $user_ids )
		)->prepare();

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

	public function get_values() {
		$options = [];

		foreach ( wp_roles()->roles as $key => $role ) {
			$options[ $key ] = translate_user_role( $role['name'] );
		}

		asort( $options );

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}