<?php

namespace ACP\Filtering\Strategy;

use ACP;
use WP_Term_Query;

/**
 * @since 4.3
 */
final class Taxonomy extends ACP\Filtering\Strategy {

	public function handle_request() {
		add_action( 'pre_get_terms', [ $this, 'handle_filter_requests' ], 1 );
	}

	/**
	 * @param WP_Term_Query $query
	 */
	public function handle_filter_requests( WP_Term_Query $query ) {
		$term_query = new ACP\TermQueryInformation();

		if ( ! $term_query->is_main_query( $query ) ) {
			return;
		}

		$list_screen = $this->get_column()->get_list_screen();

		if ( ! $list_screen instanceof ACP\ListScreen\Taxonomy ) {
			return;
		}

		if ( empty( $query->query_vars['taxonomy'] ) || ! in_array( $list_screen->get_taxonomy(), $query->query_vars['taxonomy'] ) ) {
			return;
		}

		if ( ! is_array( $query->query_vars['meta_query'] ) ) {
			$query->query_vars['meta_query'] = [];
		}

		$query->query_vars = $this->model->get_filtering_vars( $query->query_vars );
	}

	/**
	 * Get values by term field
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public function get_values_by_db_field( $field ) {
		global $wpdb;

		$term_field = sanitize_key( $field );

		$values = $wpdb->get_col( "
			SELECT DISTINCT {$term_field}
			FROM {$wpdb->terms}
			WHERE {$term_field} <> ''
			ORDER BY 1
		" );

		if ( ! $values || is_wp_error( $values ) ) {
			return [];
		}

		return $values;
	}

}