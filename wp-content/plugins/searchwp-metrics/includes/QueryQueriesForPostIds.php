<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryQueriesForPostIds
 * @package SearchWP_Metrics
 */
class QueryQueriesForPostIds extends Query {

	protected $engine;
	protected $after;
	protected $before;
	protected $limit;
	protected $post_ids;

	private $defaults = array(
		'engine' => 'default',
		'after'  => '30 days ago',
		'before' => 'now',
		'limit'  => 10,
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		$args = apply_filters( 'searchwp_metrics_query_queries_for_post_id_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT `{$this->tables['queries']}`.`query`, COUNT(`{$this->tables['queries']}`.`query`) AS `count`";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['clicks']}`";
	}

	function set_sql_join() {
		$this->sql['join'] = "LEFT JOIN `{$this->tables['ids']}` ON `{$this->tables['ids']}`.`id` = `{$this->tables['clicks']}`.`hash`";
		$this->sql['join'] .= " LEFT JOIN `{$this->tables['searches']}` ON `{$this->tables['searches']}`.`hash` = `{$this->tables['clicks']}`.`hash`";
		$this->sql['join'] .= " LEFT JOIN `{$this->tables['queries']}` ON `{$this->tables['queries']}`.`id` = `{$this->tables['searches']}`.`query`";
	}

	function set_sql_where() {
		parent::set_sql_where();
		$this->post_ids = array_map( 'absint', $this->post_ids );
		$this->post_ids = array_unique( $this->post_ids );
		$this->sql['where'] .= " AND `{$this->tables['clicks']}`.`post_id` IN (" . implode( ', ', $this->post_ids ) . ')';
		$this->sql['where'] .= " AND `{$this->tables['ids']}`.`type` = 'hash' ";
	}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY `{$this->tables['queries']}`.`query`";
	}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `count` DESC LIMIT " . absint( $this->limit );
	}
}
