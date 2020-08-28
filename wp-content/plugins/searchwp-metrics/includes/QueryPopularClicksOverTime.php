<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryPopularClicksOverTime
 * @package SearchWP_Metrics
 */
class QueryPopularClicksOverTime extends Query {

	protected $engine;
	protected $after;
	protected $before;
	protected $limit;
	private $defaults = array(
		'engine' => 'default',
		'after'  => '30 days ago',
		'before' => 'now',
		'limit'  => 10000,
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_popular_clicks_over_time_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		global $wpdb;

		$this->sql['select'] = "SELECT `{$this->tables['clicks']}`.`post_id`, $wpdb->posts.`post_title`, COUNT(`{$this->tables['clicks']}`.`post_id`) AS clicks";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['clicks']}`";
	}

	function set_sql_join() {
		global $wpdb;

		$this->sql['join'] = "LEFT JOIN $wpdb->posts ON `{$this->tables['clicks']}`.`post_id` = $wpdb->posts.`ID`";
		$this->sql['join'] .= "LEFT JOIN `{$this->tables['ids']}` ON `{$this->tables['clicks']}`.`hash` = `{$this->tables['ids']}`.`id`";
		$this->sql['join'] .= "LEFT JOIN `{$this->tables['searches']}` ON `{$this->tables['searches']}`.`hash` = `{$this->tables['ids']}`.`id`";
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['clicks']}`.`post_id`";
	}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `clicks` DESC LIMIT " . absint( $this->limit );
	}
}
