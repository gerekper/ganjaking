<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryPopularQueriesOverTime
 * @package SearchWP_Metrics
 */
class QueryPopularQueriesOverTime extends Query {

	protected $engine;
	protected $after;
	protected $before;
	protected $limit;
	protected $hits_min;
	protected $hits_max;

	private $defaults = array(
		'engine'   => 'default',
		'after'    => '30 days ago',
		'before'   => 'now',
		'limit'    => 10,
		'hits_min' => 0,  // Only show queries that have at least one result
		'hits_max' => -1, // No hit max
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_popular_queries_over_time_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT `{$this->tables['queries']}`.`query`, COUNT(`{$this->tables['searches']}`.`query`) AS searchcount";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['searches']}`";
	}

	function set_sql_join() {
		$this->sql['join'] = "LEFT JOIN `{$this->tables['queries']}` ON `{$this->tables['queries']}`.`id` = `{$this->tables['searches']}`.`query`";
	}

	function set_sql_where() {
		parent::set_sql_where();

		// If there are no hits limits in play, bail out.
		if ( intval( $this->hits_min ) < 0 && intval( $this->hits_max ) < 0 ) {
			return;
		}

		if ( intval( $this->hits_min ) >= 0 ) {
			$this->sql['where'] .= " AND `{$this->tables['searches']}`.`hits` >= " . absint( $this->hits_min );
		}

		if ( intval( $this->hits_max ) >= 0 ) {
			$this->sql['where'] .= " AND `{$this->tables['searches']}`.`hits` <= " . absint( $this->hits_max );
		}
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['searches']}`.`query`";
	}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `searchcount` DESC, `{$this->tables['queries']}`.`query` ASC LIMIT " . absint( $this->limit );
	}
}
