<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class ShippingMethod extends AbstractModel {

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
		global $wpdb;

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		$clauses['join'] .= "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_oi ON {$wpdb->posts}.ID = acsort_oi.order_id
				AND acsort_oi.order_item_type = 'shipping'
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS acsort_oim ON acsort_oi.order_item_id = acsort_oim.order_item_id
				AND acsort_oim.meta_key = 'method_id'
		";

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( 'acsort_oim.meta_value', $this->get_order() );

		return $clauses;
	}

}