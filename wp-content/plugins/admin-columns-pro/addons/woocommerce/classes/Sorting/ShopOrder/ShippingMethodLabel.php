<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class ShippingMethodLabel extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		$clauses['join'] .= "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items ON {$wpdb->posts}.ID = acsort_order_items.order_id
				AND acsort_order_items.order_item_type = 'shipping'
		";

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( 'acsort_order_items.order_item_name', $this->get_order() );

		return $clauses;
	}

}