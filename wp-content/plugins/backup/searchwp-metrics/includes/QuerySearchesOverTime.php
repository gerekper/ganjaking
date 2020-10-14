<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuerySearchesOverTime
 * @package SearchWP_Metrics
 */
class QuerySearchesOverTime extends Query {

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

		$args = apply_filters( 'searchwp_metrics_query_searches_over_time_args', $args );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function get_results( $output_type = 'OBJECT' ) {
		global $wpdb;

		$this->set_sql_fields();
		$this->set_sql_from();
		$this->set_sql_where();
		$this->set_sql_join();
		$this->set_sql_group_by();
		$this->set_sql_order_by();

		$sql = implode( ' ', $this->sql );

		$results = $wpdb->get_results( $sql, 'OBJECT_K' );

		return $this->fill_in_date_gaps( $results );
	}

	/**
	 * Fill in any missing dates (i.e. where no searches took place)
	 */
	function fill_in_date_gaps( $results ) {
		$begin     = new \DateTime( date( 'Y-m-d', strtotime( $this->after ) ) );
		$end       = new \DateTime( date( 'Y-m-d', strtotime( $this->before ) ) );
		$interval  = new \DateInterval( 'P1D' );
		$daterange = new \DatePeriod( $begin, $interval ,$end );

		foreach ( $daterange as $date ) {
			$this_date = $date->format( 'Y-m-d' );

			if ( ! array_key_exists( $this_date, $results ) ) {
				$results[ $this_date ] = new \stdClass();
				$results[ $this_date ]->date = $this_date;
				$results[ $this_date ]->engine = $this->engine;
				$results[ $this_date ]->searches = 0;
			}
		}

		ksort( $results );

		return $results;
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT DATE_FORMAT(`{$this->tables['searches']}`.`tstamp`, '%Y-%m-%d') AS date, `{$this->tables['searches']}`.`engine`, COUNT(*) AS searches";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['searches']}`";
	}

	function set_sql_join() {}

	function set_sql_group_by() {
		$this->sql['group_by'] = "GROUP BY DATE_FORMAT(`{$this->tables['searches']}`.`tstamp`, '%Y-%m-%d')";
	}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['order_by'] = "ORDER BY `date` ASC";
	}
}
