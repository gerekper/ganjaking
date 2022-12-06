<?php

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Strategy\Post;

/**
 * @property Post $strategy
 */
class SkuNaturalSort extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		$clauses['orderby'] = SqlOrderByFactory::create_with_ids( "$wpdb->posts.ID", $this->get_sorted_ids(), $this->get_order() ) ?: $clauses['orderby'];

		return $clauses;
	}

	/**
	 * @return int[]
	 */
	private function get_sorted_ids() {
		global $wpdb;

		$sql = "
			SELECT pp.ID AS id, COALESCE( NULLIF( acsort_postmeta.meta_value, '' ), acsort_parentmeta.meta_value ) AS sku
			FROM {$wpdb->posts} AS pp
			INNER JOIN {$wpdb->posts} AS acsort_parent ON acsort_parent.ID = pp.post_parent
				AND acsort_parent.post_type = 'product'
			LEFT JOIN {$wpdb->postmeta} AS acsort_postmeta ON acsort_postmeta.post_id = pp.ID 
				AND acsort_postmeta.meta_key = '_sku'
			LEFT JOIN {$wpdb->postmeta} AS acsort_parentmeta ON acsort_parentmeta.post_id = acsort_parent.ID 
				AND acsort_parentmeta.meta_key = '_sku'
			WHERE pp.post_type = 'product_variation'
		";

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( " AND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {
			return [];
		}

		$ids = [];

		foreach ( $results as $object ) {
			if ( $object->sku ) {
				$ids[ $object->id ] = $object->sku;
			}
		}

		natcasesort( $ids );

		return array_keys( $ids );
	}

}