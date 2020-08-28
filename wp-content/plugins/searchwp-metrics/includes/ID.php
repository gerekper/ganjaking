<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ID
 * @package SearchWP_Metrics
 */
class ID {

	private $type;
	private $metrics;
	private $hash;
	private $hash_id;

	/**
	 * ID constructor.
	 *
	 * @param string $type
	 */
	function __construct( $type = 'hash' ) {
		$this->type = sanitize_key( $type );

		$this->metrics = new \SearchWP_Metrics();
	}

	/**
	 * Get numeric ID from hash
	 *
	 * @param $hash
	 *
	 * @return int|null
	 */
	function get_numeric_id_from_hash( $hash = '' ) {
		global $wpdb;

		// If it's not an md5 hash it's not valid
		if ( ! preg_match( '/^[a-f0-9]{32}$/', $hash ) ) {
			return null;
		}

		$ids_table = $this->metrics->get_table_name( 'ids' );

		$id = $wpdb->get_var( $wpdb->prepare(
			"
				SELECT id
				FROM $ids_table
				WHERE value = %s AND type = %s
				",
			$hash,
			$this->type
		) );

		return is_numeric( $id ) ? absint( $id ) : null;
	}

	/**
	 * Retrieves the query from a hash ID
	 */
	function get_query_from_hash_id( $hash_id ) {
		global $wpdb;

		$ids_table      = $this->metrics->get_table_name( 'ids' );
		$searches_table = $this->metrics->get_table_name( 'searches' );
		$queries_table  = $this->metrics->get_table_name( 'queries' );

		return $wpdb->get_row(
			$wpdb->prepare("
				SELECT queries.query, searches.engine
				FROM $searches_table as searches
				LEFT JOIN $ids_table as ids ON ids.`id` = searches.`hash`
				LEFT JOIN $queries_table as queries on searches.`query` = queries.`id`
				WHERE ids.`id` = %d
				",
				$hash_id
			),
			'ARRAY_A'
		);
	}

	/**
	 * Use a search hash ID to reproduce a search to find its SERP position, DOES NOT CONSIDER PAGINATION
	 *
	 * @param $hash_id
	 *
	 * @param $post_id
	 *
	 * @return int
	 */
	function get_serp_position_from_hash_id( $hash_id, $post_id ) {
		$query = $this->get_query_from_hash_id( $hash_id );

		// Prevent unwanted logging/tracking
		add_filter( 'searchwp\statistics\log', 'searchwp_metrics_return_false', 990 );
		add_filter( 'searchwp_log_search', 'searchwp_metrics_return_false', 990 );
		add_filter( 'searchwp_metrics_log_search', 'searchwp_metrics_return_false', 990 );

		$searchwp_results = new \SWP_Query( array(
			'nopaging'  => true,
			'engine'    => $query['engine'],
			's'         => $query['query'],
			'fields'    => 'ids',
		) );

		remove_filter( 'searchwp\statistics\log', 'searchwp_metrics_return_false', 990 );
		remove_filter( 'searchwp_log_search', 'searchwp_metrics_return_false', 990 );
		remove_filter( 'searchwp_metrics_log_search', 'searchwp_metrics_return_false', 990 );

		$position = array_search( absint( $post_id ), $searchwp_results->posts, true );

		// It should be impossible for the result to not be found (because we're performing
		// the same search and then tracking a link from that search results set) but if
		// that somehow happens we'll force the position to be zero, else it's the key
		// of the resulting posts array from SWP_Query (which is index 0 so we need to +1)
		$position = false === $position ? 0 : $position + 1;

		return $position;
	}

	/**
	 * Generate a 'local' hash that depends on the visitor herself
	 *
	 * @return string
	 */
	function generate_local() {
		return $this->generate( false );
	}

	/**
	 * Get unique ID
	 *
	 * @param bool $global Whether the hash should be unique to the user or global to the install
	 *
	 * @return string
	 */
	function generate( $global = true ) {
		global $wpdb;

		do {
			$salt = time() . mt_rand() . $this->type;
			if ( ! $global ) {
				$_SERVER['REMOTE_ADDR'];
			}
			$hash = md5( $salt );
		} while ( $this->id_exists( $hash ) );

		// INSERT uid into ids table
		$wpdb->insert(
			$this->metrics->get_table_name( 'ids' ),
			array(
				'value' => $hash,
				'type'  => $this->type,
			),
			array(
				'%s',
				'%s',
			)
		);

		$this->hash = $hash;
		$this->hash_id = absint( $wpdb->insert_id );

		return $hash;
	}

	/**
	 * Getter for hash ID
	 *
	 * @return int
	 */
	function get_hash_id() {
		return absint( $this->hash_id );
	}

	/**
	 * Check to see if a specific hash exists
	 *
	 * @param $hash
	 *
	 * @return bool
	 */
	function id_exists( $hash ) {
		if ( ! preg_match( '/^[a-f0-9]{32}$/', $hash ) ) {
			return false;
		}

		return ! is_null( $this->get_numeric_id_from_hash( $hash ) );
	}
}
