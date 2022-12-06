<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class OrderWeight extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		$sql = $this->get_sorted_order_ids();

		$clauses['join'] .= "
			LEFT JOIN ( {$sql} ) AS acsort_count 
				ON {$wpdb->posts}.ID = acsort_count.ID
		";
		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_count.orderweight", $this->get_order() );

		return $clauses;
	}

	public function get_sorted_order_ids() {
		global $wpdb;

		return "
			SELECT ID, SUM(total) as orderweight
			FROM (
				SELECT woi.order_id AS ID, woim2.meta_value*pm.meta_value AS total
				FROM {$wpdb->prefix}woocommerce_order_items AS woi
				LEFT JOIN ( 
					SELECT order_item_id, meta_value AS product_id FROM (
						SELECT * FROM {$wpdb->prefix}woocommerce_order_itemmeta
						WHERE meta_key = '_product_id' OR meta_key = '_variation_id'
						ORDER BY meta_value DESC
						LIMIT 1000000000
					) AS sq
					GROUP BY order_item_id
				) AS woim ON woi.order_item_id = woim.order_item_id
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim2 ON woi.order_item_id = woim2.order_item_id AND woim2.meta_key = '_qty'
				LEFT JOIN {$wpdb->postmeta} as pm ON woim.product_id = pm.post_id AND pm.meta_key = '_weight'
				WHERE woi.order_item_type = 'line_item'
			) AS total_order_weight
			GROUP BY total_order_weight.ID
		";
	}

}