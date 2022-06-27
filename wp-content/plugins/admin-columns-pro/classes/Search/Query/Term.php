<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use ACP\TermQueryInformation;
use WP_Term_Query;

class Term extends Query {

	public function register() {
		add_action( 'pre_get_terms', [ $this, 'callback_meta_query' ], 1 );
		add_filter( 'terms_clauses', [ $this, 'callback_where' ], 1, 3 );
		add_filter( 'terms_clauses', [ $this, 'callback_join' ], 1, 3 );
	}

	public function callback_where( $pieces, $taxonomies, $args ) {
		if ( ! ( new TermQueryInformation() )->is_main_query_by_args( $args ) ) {
			return $pieces;
		}

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_where() ) {
				$pieces['where'] .= ' AND ' . $binding->get_where();
			}
		}

		return $pieces;
	}

	public function callback_join( $pieces, $taxonomies, $args ) {
		if ( ! ( new TermQueryInformation() )->is_main_query_by_args( $args ) ) {
			return $pieces;
		}

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_join() ) {
				$pieces['join'] .= "\n" . $binding->get_where();
			}
		}

		return $pieces;
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
		return ( new TermQueryInformation() )->is_main_query( $query );
	}

}