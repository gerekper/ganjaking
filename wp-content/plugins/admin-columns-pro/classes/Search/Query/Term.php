<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use ACP\TermQueryInformation;
use WP_Term_Query;

class Term extends Query {

	public function register() {
		add_action( 'pre_get_terms', [ $this, 'callback_meta_query' ], 1 );
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return void
	 */
	public function callback_meta_query( WP_Term_Query $query ) {
		if ( ! $this->is_main_query( $query ) ) {
			return;
		}

		$meta_query = $this->get_meta_query();

		if ( ! $meta_query ) {
			return;
		}

		if ( $query->query_vars['meta_query'] ) {
			$meta_query[] = $query->query_vars['meta_query'];
		}

		$query->query_vars['meta_query'] = $meta_query;
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return bool
	 */
	private function is_main_query( WP_Term_Query $query ) {
		$term_query = new TermQueryInformation();

		return $term_query->is_main_query( $query );
	}

}