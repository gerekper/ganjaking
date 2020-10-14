<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryFailedSearchesOverTime
 * @package SearchWP_Metrics
 */
class QueryFailedSearchesOverTime extends Query {

	protected $engine;
	protected $after;
	protected $before;
	protected $minimum;
	protected $limit;

	private $defaults = array(
		'engine'  => 'default',
		'after'   => '30 days ago',
		'before'  => 'now',
		'minimum' => 2,
		'limit'   => 10000,
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_failed_searches_over_time_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT `{$this->tables['queries']}`.`query`, COUNT(`{$this->tables['searches']}`.`query`) AS failcount";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['searches']}`";
	}

	function set_sql_join() {
		$this->sql['join'] = "LEFT JOIN `{$this->tables['queries']}` ON `{$this->tables['queries']}`.`id` = `{$this->tables['searches']}`.`query`";
	}

	function set_sql_where() {
		parent::set_sql_where();
		$this->sql['where'] .= " AND `{$this->tables['searches']}`.`hits` < 1 ";
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['searches']}`.`query`";
	}

	function set_sql_having() {
		$this->sql['having'] = "HAVING `failcount` >= " . absint( $this->minimum );
	}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `failcount` DESC LIMIT " . absint( $this->limit );
	}
}
