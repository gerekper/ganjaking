<?php

namespace ACA\WC\Search\ShopOrder;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class NotesSystem extends Comparison {

	public function __construct() {
		$operators = new Operators(
			[
				Operators::CONTAINS,
				Operators::NOT_CONTAINS,
				Operators::BEGINS_WITH,
				Operators::ENDS_WITH,
			], false
		);

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$alias = $bindings->get_unique_alias( 'nosy' );

		// System notes have `WooCommerce` as the author
		$join = $wpdb->prepare( "INNER JOIN $wpdb->comments AS $alias ON ( $wpdb->posts.ID = $alias.comment_post_ID AND $alias.comment_type = 'order_note' AND $alias.comment_author = %s )", __( 'WooCommerce', 'woocommerce' ) );

		$bindings->join( $join )
		         ->group_by( "$wpdb->posts.ID" );

		$comparison = ComparisonFactory::create(
			"$alias.comment_content",
			$operator,
			$value
		);

		$where[] = $comparison();

		$bindings->where( implode( ' AND ', $where ) );

		return $bindings;
	}

}