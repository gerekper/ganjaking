<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\ComputationType;

class Dimensions extends AbstractModel implements WarningAware {

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
				LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta1
					ON acsort_postmeta1.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta1.meta_key = '_length' 
				LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta2
					ON acsort_postmeta2.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta2.meta_key = '_width' 
				LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta3
					ON acsort_postmeta3.post_id = {$wpdb->posts}.ID
					AND acsort_postmeta3.meta_key = '_height' 
				";

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( '%s,%s',
			SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::SUM ), 'acsort_postmeta1.meta_value * acsort_postmeta2.meta_value * acsort_postmeta3.meta_value', $this->get_order() ),
			"{$wpdb->posts}.ID"
		);;

		return $clauses;
	}

}