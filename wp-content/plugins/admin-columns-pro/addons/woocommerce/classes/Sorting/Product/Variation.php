<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;

class Variation extends AbstractModel implements WarningAware {

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
			LEFT JOIN 
			(
				SELECT ac_variation_count.ID, count( * ) as count, post_parent
				FROM {$wpdb->posts} ac_variation_count
				WHERE 
					post_type = 'product_variation'
					AND post_status = 'publish'
				GROUP BY post_parent
			) ac_variation_count 
			ON ac_variation_count.post_parent = {$wpdb->posts}.ID
		";
		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( '%s,%s',
			SqlOrderByFactory::create( 'ac_variation_count.count', $this->get_order() ),
			"{$wpdb->posts}.ID"
		);

		return $clauses;
	}

}