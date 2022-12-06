<?php

namespace ACA\WC\Search\Product;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class OrderCount extends Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $operators, Value::INT );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();
		$alias = $bindings->get_unique_alias( 'wc_oim' );
		$join_alias = $bindings->get_unique_alias( 'product' );

		$sub_query = "
			SELECT {$alias}.meta_value AS product_id, COUNT( 1 ) as order_count
			FROM {$wpdb->prefix}woocommerce_order_itemmeta {$alias}
			WHERE {$alias}.meta_key = '_product_id'
			GROUP BY {$alias}.meta_value";

		$comparison = ComparisonFactory::create( $join_alias . '.order_count', $operator, $value );

		return $bindings->join( " INNER JOIN( {$sub_query}) AS {$join_alias} ON {$wpdb->posts}.ID = {$join_alias}.product_id" )
		                ->where( $comparison() );
	}

}