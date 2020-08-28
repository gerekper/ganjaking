<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuerySearchSearchQueries
 * @package SearchWP_Metrics
 */
class QuerySearchSearchQueries extends Query {

	protected $query;
	protected $partial;

	private $defaults = array(
		'query' => '',
		'partial' => false,
	);

	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_search_search_queries_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT *";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['queries']}`";
	}

	function set_sql_join() {}

	function set_sql_where() {
		if ( class_exists( 'SearchWP_Stats' ) ) {
			$searchwp_core_stats = new \SearchWP_Stats();
			$ignored_queries     = $searchwp_core_stats->get_ignored_queries();
			$operator            = $this->partial ? 'LIKE' : '=';
			$this->sql['where']  = "WHERE LOWER(`{$this->tables['queries']}`.`query`) " . $operator . ' LOWER(%s)';

			if ( ! empty( $ignored_queries ) ) {
				$this->sql['where'] .= " AND MD5(LOWER(`{$this->tables['queries']}`.`query`)) NOT IN ('" . implode( "', '", $ignored_queries ) . "')";
			}
		} else if ( class_exists( '\SearchWP\Statistics' ) ) {
			$ignored_queries    = array_map( 'strtolower', \SearchWP\Settings::get( 'ignored_queries', 'array' ) );
			$operator           = $this->partial ? 'LIKE' : '=';
			$this->sql['where'] = "WHERE LOWER(`{$this->tables['queries']}`.`query`) " . $operator . ' LOWER(%s)';

			if ( ! empty( $ignored_queries ) ) {
				$this->sql['where'] .= " AND LOWER(`{$this->tables['queries']}`.`query`) NOT IN ('" . implode( "', '", $ignored_queries ) . "')";
			}
		} else {
			return '';
		}
	}

	function set_sql_group_by() {}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['order_by'] = 'LIMIT 300'; // Hardcoded because it's hardcoded in Vue at 300
	}
}
