<?php

namespace ACP\Search\Comparison\Meta;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;

class User extends Meta
	implements SearchableValues {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::CURRENT_USER,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $meta_type, Value::INT );
	}

	public function get_values( $s, $paged ) {
		$entities = new Select\Entities\User( [
			'search' => $s,
			'paged'  => $paged,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Group\UserRole(
				new Formatter\UserName( $entities )
			)
		);
	}

}