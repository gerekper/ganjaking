<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Query
 * @package SearchWP_Metrics
 */
abstract class Query {

	private $metrics;
	private $tables;
	private $ignored_queries;
	private $limited_to_searches;

	protected $sql;

	public $results = array();

	/**
	 * Query constructor.
	 *
	 * @param string $type
	 */
	function __construct( $args = array() ) {
		$this->metrics = new \SearchWP_Metrics();
		$this->set_tables();
		$this->set_ignored_query_ids();
	}

	private function set_tables() {
		foreach ( $this->metrics->get_db_tables() as $table ) {
			$this->tables[ $table ] = $this->metrics->get_db_prefix() . $table;
		}
	}

	function build_sql() {
		$this->set_sql_fields();
		$this->set_sql_from();
		$this->set_sql_join();
		$this->set_sql_where();
		$this->set_sql_group_by();
		$this->set_sql_having();
		$this->set_sql_order_by();
	}

	function get_sql() {
		return implode( ' ', $this->sql );
	}

	function get_results( $output_type = 'OBJECT' ) {
		global $wpdb;

		$this->build_sql();

		$sql = implode( ' ', $this->sql );

		$this->results = $wpdb->get_results( $sql, $output_type );

		return $this->results;
	}

	function set_ignored_searchwp4() {
		global $wpdb;

		if ( ! class_exists( '\\SearchWP\\Statistics' ) ) {
			return;
		}

		$ignored_queries = \SearchWP\Settings::get( 'ignored_queries', 'array' );

		if ( empty( $ignored_queries ) ) {
			return;
		}

		$ignored_queries = array_map( 'strtolower', $ignored_queries );

		$query_ids = $wpdb->get_col(
			$wpdb->prepare("
				SELECT {$this->tables['queries']}.id
				FROM {$this->tables['queries']}
				WHERE LOWER({$this->tables['queries']}.query) IN ( " . implode( ', ', array_fill( 0, count( $ignored_queries ), '%s' ) ) . ')',
				array_values( $ignored_queries )
			)
		);

		$this->ignored_queries = array_map( 'absint', $query_ids );
	}

	function set_ignored_query_ids() {
		global $wpdb;

		if ( ! class_exists( 'SearchWP_Stats' ) ) {
			if ( class_exists( '\\SearchWP\\Statistics' ) ) {
				$this->set_ignored_searchwp4();
			}

			return;
		}

		$searchwp_core_stats = new \SearchWP_Stats();

		$ignored_queries = $searchwp_core_stats->get_ignored_queries();

		$ignored_queries = array_filter( array_values( $ignored_queries ), function( $value ) {
			return preg_match( '/^[a-f0-9]{32}$/', $value );
		} );

		if ( ! empty( $ignored_queries ) ) {
			$query_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT `{$this->tables['queries']}`.`id` FROM `{$this->tables['queries']}` WHERE md5(`{$this->tables['queries']}`.`query`) IN ( " . implode( ', ', array_fill( 0, count( $ignored_queries ), '%s' ) ) . ')',
					array_values( $ignored_queries )
				)
			);
		} else {
			$query_ids = array();
		}

		$this->ignored_queries = array_map( 'absint', $query_ids );
	}

	function get_ignored_query_ids() {
		return $this->ignored_queries;
	}

	function get_chart_labels_from_results( $results ) {

	}

	abstract protected function set_sql_fields();

	abstract protected function set_sql_from();

	abstract protected function set_sql_join();

	function set_sql_where() {
		$date_query = new \WP_Date_Query(
			array(
				array(
					'after'     => date( 'F j, Y 00:00:00', strtotime( $this->after ) ),
					'before'    => date( 'F j, Y 23:59:59', strtotime( $this->before ) ),
					'inclusive' => true,
				),
			),
			$this->tables['searches'] . '.tstamp'
		);

		$engine = function_exists( 'SWP' ) && SWP()->is_valid_engine( $this->engine ) ? $this->engine : 'default';

		$this->sql['where']   = array();
		$this->sql['where'][] = "WHERE `{$this->tables['searches']}`.`engine` = '{$this->engine}'";

		// Apply limited search terms
		$query_limiter_sql = $this->build_search_query_limiter_sql();
		if ( ! empty( $query_limiter_sql ) ) {
			$this->sql['where'][] = 'AND ' . $query_limiter_sql;
		}

		// Apply ignored
		$ignored = $this->get_ignored_query_ids();
		if ( ! empty( $ignored ) ) {
			$this->sql['where'][] = "AND `{$this->tables['searches']}`.`query` NOT IN (" . implode( ', ', $this->get_ignored_query_ids() ) . ')';
		}

		$this->sql['where'][] = $date_query->get_sql();

		$this->sql['where'] = implode( ' ', $this->sql['where'] );
	}

	/**
	 * You're allowed to limit to a query that's in the database OR add a partial match "tag" in the multiselect
	 * The limiter is passed as an array of integers. If the integer is positive it means it's in the database
	 * and if the integer is negative it means we need to do a partial match.
	 */
	private function build_search_query_limiter_sql() {
		global $wpdb;

		if ( empty( $this->limited_to_searches ) && empty( $_REQUEST['searches'] ) ) {
			return '';
		}

		// First we're going to process the limiter
		if ( empty( $this->limited_to_searches ) ) {
			$this->limited_to_searches = $_REQUEST['searches'];

			if ( ! is_array( $this->limited_to_searches ) ) {
				$this->limited_to_searches = array( $this->limited_to_searches );
			}
		} else {
			// If the limiter was passed it must be query IDs
			if ( ! is_array( $this->limited_to_searches ) ) {
				$this->limited_to_searches = array( $this->limited_to_searches );
			}

			$this->limited_to_searches = array_map( 'absint', $this->limited_to_searches );
		}

		if ( empty( $this->limited_to_searches ) ) {
			return '';
		}

		// Now we need to determine what's an exact match ID and what's a partial match string
		$exact_matches_ids = array();

		foreach ( $this->limited_to_searches as $val ) {
			if ( is_numeric( $val ) ) {
				$exact_matches_ids[] = absint( $val );
			} else {
				// We're going to search for partial matches because we need the IDs
				$search_query_search = new QuerySearchSearchQueries(array(
					'query' => $val,
					'partial' => true,
				));
				$search_query_search->build_sql();
				$sql = $search_query_search->get_sql();
				$search_query_results = $wpdb->get_results( $wpdb->prepare(
					$sql,
					'%' . $wpdb->esc_like( $val ) . '%'
				) );

				$exact_matches_ids = array_merge( wp_list_pluck( $search_query_results, 'id' ), $exact_matches_ids );
			}
		}

		$exact_matches_ids = array_map( 'absint', $exact_matches_ids );
		$exact_matches_ids = array_unique( $exact_matches_ids );

		// This should never be reached...
		if ( empty( $exact_matches_ids ) ) {
			$exact_matches_ids = array( 0 );
		}

		return "`{$this->tables['searches']}`.`query` IN (" . implode( ', ', $exact_matches_ids ) . ')';
	}

	abstract protected function set_sql_group_by();

	abstract protected function set_sql_having();

	abstract protected function set_sql_order_by();

	/**
	 * Magic getter
	 *
	 * @since 1.0
	 *
	 * @param $property
	 *
	 * @return null
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}

		return null;
	}

	/**
	 * Magic setter
	 *
	 * @since 1.0
	 *
	 * @param $property
	 * @param $value
	 *
	 * @return $this
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		}

		return $this;
	}
}
