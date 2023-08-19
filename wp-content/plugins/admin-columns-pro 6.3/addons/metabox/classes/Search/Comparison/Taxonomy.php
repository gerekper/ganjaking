<?php

namespace ACA\MetaBox\Search\Comparison;

use ACP;

class Taxonomy extends ACP\Search\Comparison\Post\Taxonomy {

	/**
	 *
	 */
	private function get_term_by_id( $term_id ) {
		global $wpdb;

		$_tax = $wpdb->get_row( $wpdb->prepare( "
			SELECT t.* 
			FROM $wpdb->term_taxonomy AS t 
			WHERE t.term_id = %s 
			LIMIT 1"
			, $term_id ) );

		if ( ! $_tax || is_wp_error( $_tax ) ) {
			return false;
		}

		return get_term( $term_id, $_tax->taxonomy );
	}

	protected function create_query_bindings( $operator, ACP\Search\Value $value ) {
		$bindings = new ACP\Search\Query\Bindings\Post();
		$term = $this->get_term_by_id( $value->get_value() );

		if ( $term ) {
			$tax_query = ACP\Search\Helper\TaxQuery\ComparisonFactory::create(
				$term->taxonomy,
				$operator,
				$value
			);

			$bindings = new ACP\Search\Query\Bindings\Post();
			$bindings->tax_query( $tax_query->get_expression() );
		}

		return $bindings;
	}

}