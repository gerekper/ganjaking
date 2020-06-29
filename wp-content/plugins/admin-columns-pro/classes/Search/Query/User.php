<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use WP_User_Query;

class User extends Query {

	public function register() {
		add_filter( 'users_list_table_query_args', [ $this, 'mark_as_table_query' ] );
		add_action( 'pre_get_users', [ $this, 'callback_meta_query' ], 1 );
		add_action( 'pre_user_query', [ $this, 'callback_where' ], 1 );
		add_action( 'pre_user_query', [ $this, 'callback_join' ], 1 );
	}

	/**
	 * Marks the main list table query as such
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function mark_as_table_query( $args ) {
		$args['is_list_table_query'] = 1;

		return $args;
	}

	/**
	 * @param WP_User_Query $query
	 *
	 * @return void
	 */
	public function callback_meta_query( WP_User_Query $query ) {
		if ( ! $this->is_table_query( $query ) ) {
			return;
		}

		$meta_query = $this->get_meta_query();

		if ( ! $meta_query ) {
			return;
		}

		if ( isset( $query->query_vars['meta_query'] ) && ! empty( $query->query_vars['meta_query'] ) ) {
			$meta_query[] = $query->query_vars['meta_query'];
		}

		$query->query_vars['meta_query'] = $meta_query;
	}

	/**
	 * @param WP_User_Query $query
	 *
	 * @return void
	 */
	public function callback_where( WP_User_Query $query ) {
		if ( ! $this->is_table_query( $query ) ) {
			return;
		}

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_where() ) {
				$query->query_where .= ' AND ' . $binding->get_where();
			}
		}
	}

	/**
	 * @param WP_User_Query $query
	 *
	 * @return void
	 */
	public function callback_join( WP_User_Query $query ) {
		if ( ! $this->is_table_query( $query ) ) {
			return;
		}

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_join() ) {
				$query->query_from .= "\n" . $binding->get_join();
			}
		}
	}

	/**
	 * @param WP_User_Query $query
	 *
	 * @return bool
	 */
	private function is_table_query( WP_User_Query $query ) {
		return isset( $query->query_vars['is_list_table_query'] ) && 1 === $query->query_vars['is_list_table_query'];
	}

}