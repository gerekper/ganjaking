<?php

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Sorting\AbstractModel;

class SKU extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$clauses['fields'] .= ", COALESCE( NULLIF( acsort_postmeta.meta_value, '' ), acsort_parentmeta.meta_value ) AS acsort_sku";
		$clauses['join'] .= " 
			LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta ON acsort_postmeta.post_id = {$wpdb->posts}.ID 
				AND acsort_postmeta.meta_key = '_sku'
			INNER JOIN {$wpdb->posts} AS acsort_parent ON acsort_parent.ID = {$wpdb->posts}.post_parent
				AND acsort_parent.post_type = 'product'
			LEFT JOIN {$wpdb->postmeta} AS acsort_parentmeta ON acsort_parentmeta.post_id = acsort_parent.ID 
				AND acsort_parentmeta.meta_key = '_sku'
		";
		$clauses['orderby'] = sprintf( "acsort_sku %s, {$wpdb->posts}.ID", $this->get_order() );
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}