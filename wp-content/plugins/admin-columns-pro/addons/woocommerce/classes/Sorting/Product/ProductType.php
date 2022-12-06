<?php

namespace ACA\WC\Sorting\Product;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class ProductType extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( '%s,%s',
			SqlOrderByFactory::create_with_ids( "{$wpdb->posts}.ID", $this->get_sorted_product_ids(), $this->get_order() ),
			"{$wpdb->posts}.ID"
		);

		return $clauses;
	}

	private function get_product_types_from_database() {
		global $wpdb;

		$sql = "SELECT t.term_id, t.slug
				FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy as tt ON (t.term_id = tt.term_taxonomy_id)
				WHERE tt.taxonomy = 'product_type'";

		$types = [];

		foreach ( $wpdb->get_results( $sql ) as $result ) {
			$types[ $result->slug ] = $result->term_id;
		}

		return $types;
	}

	public function get_sorted_product_ids() {
		global $wpdb;

		$ids = [];
		$db_types = $this->get_product_types_from_database();
		$wc_types = wc_get_product_types();
		natsort( $wc_types );

		if ( 'DESC' === $this->get_order() ) {
			$wc_types = array_reverse( $wc_types );
		}

		foreach ( $wc_types as $name => $label ) {
			if ( array_key_exists( $name, $db_types ) ) {
				$ids[] = $db_types[ $name ];
			}
		}

		$db_ids = implode( ',', array_values( $db_types ) );
		$ids = implode( ',', $ids );

		$sql = "SELECT p.ID 
				FROM $wpdb->posts AS p
				INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id)
				WHERE p.post_type = 'product' 
				AND tr.term_taxonomy_id IN ( $db_ids )
				ORDER BY FIELD( tr.term_taxonomy_id, $ids)";

		return array_map( 'intval', $wpdb->get_col( $sql ) );
	}

}