<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ClickBuoy
 * @package SearchWP_Metrics
 */
class ClickBuoy {
	private $metrics;
	private $id;
	private $search_query;
	private $modifier = 1;

	/**
	 * ClickBuoy constructor.
	 *
	 * @param string $type
	 */
	function __construct() {
		$this->metrics = new \SearchWP_Metrics();
		$this->id = new \SearchWP_Metrics\ID();
	}

	function init() {
		add_action( 'searchwp_metrics_click', array( $this, 'track_click' ) );

		// These hooks implement the buoy
		add_filter( 'searchwp\query\search_string', array( $this, 'store_search_query' ), 10, 2 );
		add_filter( 'searchwp\query\mods', array( $this, 'implement_mod' ), 10, 2 );

		// SearchWP 3.x compat.
		add_filter( 'searchwp_pre_search_terms', array( $this, 'store_search_query' ), 10, 2 );
		add_filter( 'searchwp_query_join', array( $this, 'join_meta_table' ), 10, 2 );
		add_filter( 'searchwp_weight_mods', array( $this, 'add_click_weight' ) );
		add_filter( 'searchwp_post_type_group_by_clause', array( $this, 'group_by' ) );
	}

	function implement_mod( $mods, $query ) {
		global $wpdb;

		$meta_key = $this->get_meta_key_for_query( $this->search_query );

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->postmeta );
		$mod->on( 'post_id', [ 'column' => 'id' ] );
		$mod->on( 'meta_key', [ 'value' => $meta_key ] );
		$mod->weight( function( $mod, $args ) {
			return "( {$this->modifier} * ( COALESCE({$mod->get_local_table_alias()}.meta_value, 0) ) )";
		} );

		$mods[] = $mod;

		return $mods;
	}

	function store_search_query( $terms, $context ) {
		$this->search_query = is_array( $terms ) ? implode( ' ', $terms ) : trim( $terms );

		return $terms;
	}

	/**
	 * Returns meta key to use for submitted query
	 */
	function get_meta_key_for_query( $query ) {
		return $this->metrics->get_db_prefix() . 'click_buoy_' . md5( $query );
	}

	/**
	 * Callback to click event to increment the click count buoy stored as post meta
	 */
	function track_click( $args ) {
		// The meta key will be based on a hash of the original search query in case Metrics data
		// is reset; if that happens the IDs will no longer match and the buoy would be all wrong
		$query_from_hash = $this->id->get_query_from_hash_id( absint( $args['hash'] ) );
		$meta_key = $this->get_meta_key_for_query( $query_from_hash['query'] );

		// Determine the current click count and increment
		$current_click_count = get_post_meta( $args['post_id'], $meta_key, true );
		$current_click_count = empty( $current_click_count ) ? 1 : absint( $current_click_count ) + 1;

		update_post_meta( $args['post_id'], $meta_key, $current_click_count );
	}

	/**
	 * JOIN to the meta table so we can incorporate our clicks
	 */
	function join_meta_table( $sql, $engine ) {
		global $wpdb;

		$meta_key = $this->get_meta_key_for_query( $this->search_query );

		$sql = $sql . " LEFT JOIN {$wpdb->postmeta} as searchwpbuoymeta ON {$wpdb->posts}.ID = searchwpbuoymeta.post_id AND searchwpbuoymeta.meta_key = '{$meta_key}'";

		return $sql;
	}
	/**
	 * Applies more weight based on total number of clicks for this search query
	 */
	function add_click_weight( $sql ) {
		global $wpdb;

		$sql .= " + ( {$this->modifier} * ( COALESCE(searchwpbuoymeta.meta_value, 0) ) ) ";

		return $sql;
	}

	/**
	 * As of MySQL 5.7 full group by is now default, so we need to accommodate our modifications
	 */
	function group_by( $clause ) {
		global $wpdb;

		$clause[] = "searchwpbuoymeta.meta_value";

		return $clause;
	}
}
