<?php

namespace ACA\WC\Search\ShopOrder;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ShippingMethodLabel extends Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$alias_order = $bindings->get_unique_alias( 'smoi' );

		$comparison = ComparisonFactory::create( "{$alias_order}.order_item_name", $operator, $value );
		$bindings->where( $comparison() );

		$join = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS {$alias_order} ON ( {$wpdb->posts}.ID = {$alias_order}.order_id ) AND {$alias_order}.order_item_type = 'shipping' AND {$comparison()} ";

		$bindings->join( $join );

		return $bindings;
	}

}