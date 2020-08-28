<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryAverageClicksPerSearch
 * @package SearchWP_Metrics
 */
class QueryAverageClicksPerSearch extends Query {

	protected $engine;
	protected $after;
	protected $before;

	private $defaults = array(
		'engine' => 'default',
		'after'  => '30 days ago',
		'before' => 'now',
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_average_clicks_per_search_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT COUNT(`{$this->tables['clicks']}`.`hash`) as `clicks`";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['clicks']}`";
	}

	function set_sql_join() {
		$this->sql['join'] = "LEFT JOIN `{$this->tables['searches']}` ON `{$this->tables['searches']}`.`hash` = `{$this->tables['clicks']}`.`hash`";
	}

	function set_sql_where() {
		parent::set_sql_where();
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['clicks']}`.`hash`";
	}

	function set_sql_having() {}

	function set_sql_order_by() {}
}
