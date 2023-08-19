<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use AC;
use ACP;
use ACP\Search\Value;

class User extends TableStorage implements ACP\Search\Comparison\SearchableValues {

	/**
	 * @var array
	 */
	protected $query_args;

	public function __construct( $operators, $table, $column, $query_args = [], $value_type = Value::INT ) {
		$this->query_args = $query_args;

		parent::__construct( $operators, $table, $column, $value_type );
	}

	public function get_values( $search, $page ) {
		$args = wp_parse_args( $this->query_args, [
			'search' => $search,
			'paged'  => $page,
		] );

		$entities = new ACP\Helper\Select\Entities\User( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\UserRole(
				new ACP\Helper\Select\Formatter\UserName( $entities )
			)
		);
	}

}