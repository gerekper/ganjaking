<?php

namespace ACA\BP\Search\User;

use AC;
use ACA\BP\Helper\Select;
use ACA\BP\Helper\Select\Formatter;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Groups extends Comparison
	implements Comparison\SearchableValues {

	public function __construct() {

		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb, $bp;

		$bindings = new Bindings();

		switch ( $operator ) {
			case Operators::EQ:
				$bindings->join( $wpdb->prepare( "
					INNER JOIN {$bp->groups->table_name_members} AS bptm ON {$wpdb->users}.ID = bptm.user_id 
					AND bptm.group_id = %d AND bptm.is_banned = 0
					", (int) $value->get_value() ) );

				break;
			case Operators::IS_EMPTY:
				$bindings->where( "NOT EXISTS( SELECT user_id FROM {$bp->groups->table_name_members} WHERE user_id = {$wpdb->users}.ID )" );

				break;
			case Operators::NOT_IS_EMPTY:
				$bindings->join( "INNER JOIN {$bp->groups->table_name_members} AS bptm ON {$wpdb->users}.ID = bptm.user_id AND is_confirmed = 1 AND is_banned = 0" );

				break;
		}

		return $bindings;
	}

	public function get_values( $search, $page ) {
		$entities = new Select\Entities\Group( [
			'search_terms' => $search,
			'page'         => $page,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Formatter\Group( $entities )
		);
	}
}