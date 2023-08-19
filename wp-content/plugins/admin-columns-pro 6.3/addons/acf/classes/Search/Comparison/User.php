<?php

namespace ACA\ACF\Search\Comparison;

use AC;
use ACP;
use ACP\Search\Operators;

class User extends ACP\Search\Comparison\Meta
	implements ACP\Search\Comparison\SearchableValues {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::CURRENT_USER,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
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