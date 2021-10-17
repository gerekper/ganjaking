<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Author extends Field
	implements SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
			Operators::CURRENT_USER,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'user_id';
	}

	/**
	 * @return array
	 */
	private function get_user_ids() {
		global $wpdb;

		return $wpdb->get_col( "SELECT DISTINCT user_id FROM {$wpdb->prefix}comments;" );
	}

	public function get_values( $search, $paged ) {
		$args = compact( 'search', 'paged' );

		$args['include'] = $this->get_user_ids();

		$entities = new Select\Entities\User( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

}