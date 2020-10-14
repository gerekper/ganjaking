<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryUnderdogs
 * @package SearchWP_Metrics
 */
class QueryUnderdogs extends Query {

	protected $engine;
	protected $after;
	protected $before;
	protected $limit;
	protected $min_avg_rank;
	protected $position_min;
	private $defaults = array(
		'engine'       => 'default',
		'after'        => '30 days ago',
		'before'       => 'now',
		'limit'        => 1000,
		'min_avg_rank' => 4,
		'position_min' => 3,
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_underdogs_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		global $wpdb;

		$this->sql['select'] = "SELECT `{$this->tables['clicks']}`.`post_id`, $wpdb->posts.`post_title`, COUNT(`{$this->tables['clicks']}`.`post_id`) AS `click_count`, AVG(`{$this->tables['clicks']}`.`position`) AS `avg_rank`";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['clicks']}`";
	}

	function set_sql_join() {
		global $wpdb;

		$this->sql['join'] = "LEFT JOIN $wpdb->posts ON `{$this->tables['clicks']}`.`post_id` = $wpdb->posts.`ID` ";
		$this->sql['join'] .= "LEFT JOIN `{$this->tables['searches']}` ON `{$this->tables['searches']}`.`hash` = `{$this->tables['clicks']}`.`hash` ";
	}

	function set_sql_where() {
		parent::set_sql_where();
		$this->sql['where'] .= " AND `{$this->tables['clicks']}`.`position` > " . absint( $this->position_min );
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['clicks']}`.`post_id`";
	}

	function set_sql_having() {
		$this->sql['having'] = 'HAVING `avg_rank` > ' . absint( $this->min_avg_rank );
	}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `click_count` DESC LIMIT " . absint( $this->limit );
	}
}
