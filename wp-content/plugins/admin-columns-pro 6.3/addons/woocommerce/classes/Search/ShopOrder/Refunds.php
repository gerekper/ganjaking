<?php

namespace ACA\WC\Search\ShopOrder;

use ACP\Search\Comparison;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Refunds extends Comparison {

	public function __construct() {
		parent::__construct(
			new Operators(
				[
					Operators::IS_EMPTY,
					Operators::NOT_IS_EMPTY,
				]
			), null,
			new Labels( [
				Operators::IS_EMPTY     => sprintf( __( 'Without %s', 'codepress-admin-columns' ), __( 'Refunds', 'codepress-admin-columns' ) ),
				Operators::NOT_IS_EMPTY => sprintf( __( 'Has %s', 'codepress-admin-columns' ), __( 'Refunds', 'codepress-admin-columns' ) ),
			] )
		);
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$sub_query = "
			SELECT DISTINCT(post_parent)
			FROM {$wpdb->posts}
			WHERE post_type = 'shop_order_refund'
		";

		$compare = Operators::NOT_IS_EMPTY === $operator ? 'IN' : 'NOT IN';
		$bindings->where( "{$wpdb->posts}.ID {$compare} ( {$sub_query} )" );

		return $bindings;
	}

}