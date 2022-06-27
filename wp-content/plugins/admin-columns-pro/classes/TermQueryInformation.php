<?php

namespace ACP;

use AC\Registrable;
use WP_Term_Query;

final class TermQueryInformation implements Registrable {

	const KEY = 'ac_is_main_term_query';

	public function register() {
		add_action( 'parse_term_query', [ $this, 'check_if_main_query' ], 1 );
	}

	public function check_if_main_query( WP_Term_Query $query ) {
		if ( ! isset( $query->query_vars['echo'] ) && ( 'all' === $query->query_vars['fields'] || 'count' === $query->query_vars['fields'] ) ) {
			$this->set_main_query( $query );
		}
	}

	/**
	 * @param WP_Term_Query $query
	 */
	private function set_main_query( WP_Term_Query $query ) {
		$query->query_vars[ self::KEY ] = true;
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return bool
	 */
	public function is_main_query( WP_Term_Query $query ) {
		return isset( $query->query_vars[ self::KEY ] ) && $query->query_vars[ self::KEY ];
	}

	public function is_main_query_by_args( $args ) {
		return isset( $args[ self::KEY ] ) && $args[ self::KEY ];
	}

}