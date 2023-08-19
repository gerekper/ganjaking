<?php

namespace ACA\WC\Search\Comment;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ProductReview extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$alias = $bindings->get_unique_alias( 'productreview' );

		$bindings->join( " JOIN {$wpdb->posts} as {$alias} ON {$wpdb->comments}.comment_post_ID = {$alias}.ID" );

		switch ( $value->get_value() ) {
			case 'yes' :
				$bindings->where( "{$alias}.post_type = 'product'" );
				break;
			default :
				$bindings->where( "{$alias}.post_type != 'product'" );
		}

		return $bindings;
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'yes' => __( 'True', 'codepress-admin-columns' ),
			'no'  => __( 'False', 'codepress-admin-columns' ),
		] );
	}

}