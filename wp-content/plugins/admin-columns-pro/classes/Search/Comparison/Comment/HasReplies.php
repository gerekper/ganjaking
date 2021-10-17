<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class HasReplies extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings\Comment();

		$ids = $this->get_comments_with_replies();

		switch ( $value->get_value() ) {
			case '1':
				$where = 'comment_ID IN(' . implode( ',', $ids ) . ')';
				break;
			default:
				$where = 'comment_ID NOT IN(' . implode( ',', $ids ) . ')';
		}

		return $bindings->where( $where );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'1' => __( 'True', 'codepress-admin-columns' ),
			'0' => __( 'False', 'codepress-admin-columns' ),
		] );
	}

	private function get_comments_with_replies() {
		global $wpdb;

		$ids = $wpdb->get_col( "
			SELECT comment_parent
			FROM {$wpdb->comments}
			WHERE comment_parent != 0
			GROUP BY comment_parent"
		);

		return $ids;
	}

}