<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;

class ItemCount extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	/**
	 * Setup clauses to sort by parent
	 *
	 * @param array $clauses array
	 *
	 * @return array
	 * @since 4.0
	 */
	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items ON {$wpdb->posts}.ID = acsort_order_items.order_id
				AND acsort_order_items.order_item_type = 'line_item'
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS acsort_order_itemmeta ON acsort_order_itemmeta.order_item_id = acsort_order_items.order_item_id
				AND acsort_order_itemmeta.meta_key = '_qty'
		";
		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::SUM ), 'acsort_order_itemmeta.meta_value', $this->get_order() );

		return $clauses;
	}

}