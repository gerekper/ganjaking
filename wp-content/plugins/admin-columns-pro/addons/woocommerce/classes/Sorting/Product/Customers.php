<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;

class Customers extends AbstractModel {

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

		$clauses['fields'] .= ", COUNT( DISTINCT acsort_order_postmeta.meta_value ) AS acsort_user_count";
		$clauses['join'] .= "
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS acsort_order_meta
					ON acsort_order_meta.meta_key = '_product_id' AND acsort_order_meta.meta_value = {$wpdb->posts}.ID
				LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items
			        ON acsort_order_meta.order_item_id = acsort_order_items.order_item_id AND acsort_order_items.order_item_type = 'line_item'
			    LEFT JOIN {$wpdb->posts} AS ac_orders
			        ON ac_orders.ID = acsort_order_items.order_id AND ac_orders.post_status = 'wc-completed'
				LEFT JOIN {$wpdb->postmeta} AS acsort_order_postmeta
			        ON ac_orders.ID = acsort_order_postmeta.post_id AND acsort_order_postmeta.meta_key = '_customer_user'";

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( "acsort_user_count %s, {$wpdb->posts}.ID", $this->get_order() );

		return $clauses;
	}

}