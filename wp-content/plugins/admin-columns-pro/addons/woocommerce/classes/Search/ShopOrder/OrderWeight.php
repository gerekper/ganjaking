<?php

namespace ACA\WC\Search\ShopOrder;

use ACP;
use ACP\Search\Comparison;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class OrderWeight extends Comparison {

	public function __construct() {
		$operators = new ACP\Search\Operators(
			[
				ACP\Search\Operators::LT,
				ACP\Search\Operators::GT,
				ACP\Search\Operators::BETWEEN,
			]
		);

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();

		return $bindings->where( $this->get_where( $operator, $value ) );
	}

	public function get_where( $operator, $value ) {
		global $wpdb;

		$order_ids = $this->get_order_ids( $operator, $value );
		$order_ids = array_filter( $order_ids, 'is_numeric' );

		// Force empty results when not IDs are found
		if ( empty( $order_ids ) ) {
			$order_ids = [ 0 ];
		}

		return sprintf( "{$wpdb->posts}.ID IN( %s )", implode( ',', $order_ids ) );
	}

	public function get_order_ids( $operator, Value $value ) {
		global $wpdb;

		switch ( $operator ) {
			case ACP\Search\Operators::BETWEEN:
				$where = $wpdb->prepare( 'total BETWEEN %d AND %d', $value->get_value()[0], $value->get_value()[1] );
				break;
			default:
				$where = $wpdb->prepare( "total {$operator} %d", $value->get_value() );
		}

		// The sub query needs a limit in order to sort the subquery before grouping it, which is necessary in this case
		$sql = "
		SELECT ID, SUM(total) AS total
		FROM (
			SELECT woi.order_id AS ID, woim2.meta_value*pm.meta_value AS total
			FROM {$wpdb->prefix}woocommerce_order_items AS woi
			JOIN ( 
				SELECT order_item_id, meta_value AS product_id FROM (
					SELECT * FROM {$wpdb->prefix}woocommerce_order_itemmeta
					WHERE meta_key = '_product_id' OR meta_key = '_variation_id'
					ORDER BY meta_value DESC
					LIMIT 1000000000
				) AS sq
				GROUP BY order_item_id
			) AS woim ON woi.order_item_id = woim.order_item_id
			JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim2 ON woi.order_item_id = woim2.order_item_id AND woim2.meta_key = '_qty'
			JOIN {$wpdb->postmeta} AS pm ON woim.product_id = pm.post_id AND pm.meta_key = '_weight'
			WHERE woi.order_item_type = 'line_item'
		) AS total_order_weight
		GROUP BY total_order_weight.ID
		HAVING {$where}
		";

		return $wpdb->get_col( $sql );
	}

}