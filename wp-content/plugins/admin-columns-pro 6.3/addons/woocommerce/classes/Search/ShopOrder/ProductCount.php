<?php

namespace ACA\WC\Search\ShopOrder;

use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ProductCount extends Comparison {

	public function __construct() {
		$operators = new ACP\Search\Operators(
			[
				ACP\Search\Operators::EQ,
				ACP\Search\Operators::LT,
				ACP\Search\Operators::GT,
				ACP\Search\Operators::BETWEEN,
			]
		);

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();
		$order_ids = $this->get_filtered_order_ids( $operator, $value );

		if ( empty( $order_ids ) ) {
			$order_ids = [ 0 ];
		}

		$order_ids = array_filter( $order_ids, 'is_numeric' );

		return $bindings->where( $wpdb->posts . '.ID IN( ' . implode( ',', $order_ids ) . ')' );
	}

	private function get_filtered_order_ids( $operator, Value $value ) {
		global $wpdb;

		switch ( $operator ) {
			case Operators::LT;
				$having = sprintf( 'HAVING products < %d', $value->get_value() );
				break;
			case Operators::GT;
				$having = sprintf( 'HAVING products > %d', $value->get_value() );
				break;
			case Operators::BETWEEN:
				$values = $value->get_value();
				$having = sprintf( 'HAVING products >= %d AND products <= %s', $values[0], $values[1] );

				break;
			default:
				$having = sprintf( 'HAVING products = %d', $value->get_value() );
		}

		$sql = "SELECT oi.order_id,SUM( oim.meta_value ) as products
                FROM {$wpdb->prefix}woocommerce_order_items AS oi
                  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
                WHERE oi.order_item_type = 'line_item'
                  AND oim.meta_key = '_qty'
                GROUP BY oi.order_id
                {$having}";

		return $wpdb->get_col( $sql );
	}

}