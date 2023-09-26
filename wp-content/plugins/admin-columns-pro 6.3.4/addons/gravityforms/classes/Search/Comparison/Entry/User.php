<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use AC;
use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class User extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues {

	public function __construct() {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::CURRENT_USER,
		] );

		parent::__construct( $operators, ACP\Search\Value::STRING );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$comparison = ACP\Search\Helper\Sql\ComparisonFactory::create( 'created_by', $operator, $value );

		return ( new Bindings )->where( $comparison() );
	}

	public function get_values( $search, $paged ) {
		$entities = new ACP\Helper\Select\Entities\User( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\UserRole(
				new ACP\Helper\Select\Formatter\UserName( $entities )
			)
		);
	}

}