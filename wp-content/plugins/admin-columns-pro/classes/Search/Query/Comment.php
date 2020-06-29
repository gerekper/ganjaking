<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use WP_Comment_Query;

class Comment extends Query {

	public function register() {
		add_action( 'pre_get_comments', [ $this, 'callback_meta_query' ], 1 );
		add_action( 'pre_get_comments', [ $this, 'callback_parent' ], 1 );
		add_filter( 'comments_clauses', [ $this, 'callback_clauses' ], 20 );
	}

	/**
	 * @param WP_Comment_Query $query
	 */
	public function callback_meta_query( WP_Comment_Query $query ) {

		$meta_query = $this->get_meta_query();

		if ( ! $meta_query ) {
			return;
		}

		if ( ! empty( $query->query_vars['meta_query'] ) ) {
			$meta_query[] = $query->query_vars['meta_query'];
		}

		$query->query_vars['meta_query'] = $meta_query;
	}

	/**
	 * @param WP_Comment_Query $query
	 */
	public function callback_parent( WP_Comment_Query $query ) {

		foreach ( $this->bindings as $binding ) {
			if ( $binding instanceof Query\Bindings\Comment && $binding->get_parent() ) {
				$query->query_vars['parent'] = $binding->get_parent();
			}
		}
	}

	/**
	 * @param array $comments_clauses
	 *
	 * @return array
	 */
	public function callback_clauses( array $comments_clauses ) {

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_where() ) {
				$comments_clauses['where'] .= ' AND ' . $binding->get_where();
			}

			if ( $binding->get_join() ) {
				$comments_clauses['join'] .= "\n" . $binding->get_join();
			}
		}

		return $comments_clauses;
	}

}