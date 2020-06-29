<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ReplyTo extends Comparison
	implements Comparison\SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings\Comment();

		return $bindings->parent( $value->get_value() );
	}

	public function get_values( $search, $paged ) {
		$args = compact( 'search', 'paged' );

		$args['comment__in'] = $this->get_parents();

		$entities = new Select\Entities\Comment( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\CommentSummary( $entities )
		);
	}

	/**
	 * @return array|false
	 */
	private function get_parents() {
		global $wpdb;

		$limit = 5000;

		$results = $wpdb->get_col( "
			SELECT DISTINCT( comment_parent )
			FROM {$wpdb->comments}
			WHERE comment_parent != '' 
			LIMIT {$limit}
		" );

		if ( ! $results || $limit <= count( $results ) ) {
			return false;
		}

		return $results;
	}

}