<?php

namespace SearchWP_Metrics;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Utilities {
	private $settings;
	private $queries;
	private $after = '30 days ago';
	private $before = 'now';
	private $engines = array( 'default' );
	private $limit = 10;

	/**
	 * Utilities constructor.
	 */
	function __construct() {
		$this->settings = new Settings();
	}

	/**
	 * Initializer
	 */
	function init() {
		add_action( 'wp_ajax_searchwp_metrics', array( $this, 'get_metrics' ) );
		add_action( 'wp_ajax_searchwp_metrics_ignore_query', array( $this, 'add_ignored_query' ) );
		add_action( 'wp_ajax_searchwp_metrics_unignore_query', array( $this, 'remove_ignored_query' ) );
		add_action( 'wp_ajax_searchwp_metrics_search_queries', array( $this, 'find_search_queries' ) );
		add_action( 'wp_ajax_searchwp_metrics_popular_search_details', array( $this, 'get_popular_search_details' ) );
		add_action( 'wp_ajax_searchwp_metrics_clear_metrics_data', array( $this, 'clear_metrics_data' ) );
		add_action( 'wp_ajax_searchwp_metrics_clear_ignored_queries', array( $this, 'clear_ignored_queries' ) );
		add_action( 'wp_ajax_searchwp_metrics_update_logging_rules', array( $this, 'update_logging_rules' ) );
		add_action( 'wp_ajax_searchwp_metrics_update_settings', array( $this, 'update_settings' ) );
	}

	/**
	 * Callback for ajax endpoint to save general settings
	 */
	function update_settings() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		$settings_cap = apply_filters( 'searchwp_metrics_capability_settings', 'manage_options' );
		if ( ! current_user_can( $settings_cap ) ) {
			wp_send_json_error( __( 'Unable to save settings', 'searchwp-metrics' ) );
		}

		$clear_data_on_uninstall = isset( $_REQUEST['clear_data_on_uninstall'] ) ? $_REQUEST['clear_data_on_uninstall'] : false;
		$click_tracking_buoy = isset( $_REQUEST['click_tracking_buoy'] ) ? $_REQUEST['click_tracking_buoy'] : false;

		$metrics = new \SearchWP_Metrics();
		$metrics->save_boolean_option( 'clear_data_on_uninstall', $clear_data_on_uninstall );
		$metrics->save_boolean_option( 'click_tracking_buoy', $click_tracking_buoy );

		wp_send_json_success();
	}

	/**
	 * Callback for ajax endpoint to save the logging rules (blocklists)
	 */
	function update_logging_rules() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		$ips   = isset( $_REQUEST['ips'] )   ? trim( stripslashes( $_REQUEST['ips'] ) ) : '';
		$roles = isset( $_REQUEST['roles'] ) ? trim( stripslashes( $_REQUEST['roles'] ) ) : '';

		if ( ! empty( $ips ) ) {
			$ips = explode( "\n", $ips );

			$ips = array_filter( $ips, function( $ip ) {
				return filter_var( $ip, FILTER_VALIDATE_IP );
			} );
		}

		if ( ! empty( $roles ) ) {
			$roles = explode( "\n", $roles );

			$roles = array_filter( $roles, function( $role ) {
				return ( is_numeric( $role ) && false !== get_userdata( $role ) ) || ! is_null( get_role( strtolower( $role ) ) );
			} );
		}

		$metrics = new \SearchWP_Metrics();
		$metrics->save_option( 'blocklists', array(
			'ips'   => $ips,
			'roles' => $roles,
		) );

		wp_send_json_success();
	}

	/**
	 * Callback for ajax endpoint to remove all of the ignored queries
	 */
	function clear_ignored_queries() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		if ( ! class_exists( 'SearchWP_Stats' ) || ! defined( 'SEARCHWP_PREFIX' ) ) {
			wp_send_json_error();
		}

		$searchwp_core_stats = new \SearchWP_Stats();

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

		update_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', array() );

		wp_send_json_success();
	}

	/**
	 * Callback for ajax endpoint to clear all Metrics data (not ignored queries)
	 */
	function clear_metrics_data( $uninstalling = false ) {
		global $wpdb;

		$metrics = new \SearchWP_Metrics();

		if ( ! $uninstalling ) {
			check_ajax_referer( 'searchwp_metrics_ajax' );

			// Truncate all custom database tables. If uninstalling they're going to get DROPPED.
			foreach ( $metrics->get_db_tables() as $table ) {
				$table = $metrics->get_db_prefix() . $table;

				$wpdb->query( "TRUNCATE TABLE {$table}" );
			}
		}

		$meta_key = str_replace( '_', '\_', $metrics->get_db_prefix() . 'click_buoy_' ) . '%';

		// Remove all click tracking metadata
		$wpdb->query(
			"DELETE FROM $wpdb->postmeta
				WHERE meta_key LIKE '" . $meta_key . "'"
		);

		if ( ! $uninstalling ) {
			wp_send_json_success();
		}
	}

	/**
	 * Returns whether this is a legacy version of SearchWP.
	 *
	 * @since 1.3
	 * @return bool
	 */
	function is_legacy_searchwp() {
		return ! ( defined( 'SEARCHWP_VERSION' ) && version_compare( SEARCHWP_VERSION, '3.99.0', '>=' ) );
	}

	/**
	 * Callback for ajax endpoint to retrieve details for Popular searches for a particular engine
	 */
	function get_popular_search_details() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		$after  = isset( $_REQUEST['after'] )  ? $_REQUEST['after']  : '30 days ago';
		$before = isset( $_REQUEST['before'] ) ? $_REQUEST['before'] : 'now';
		$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : '';
		$limit  = isset( $_REQUEST['limit'] )  ? $_REQUEST['limit']  : $this->limit;

		if( ! $this->is_legacy_searchwp() ) {
			if ( ! defined( 'SEARCHWP_VERSION' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! \SearchWP\Settings::get_engine_settings( $engine ) ) {
				wp_send_json_error(
					__( 'An invalid engine was passed to get_popular_search_details()', 'searchwp-metrics' )
				);
			}
		} else {
			if ( ! function_exists( 'SWP' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! SWP()->is_valid_engine( $engine ) ) {
				wp_send_json_error(
					__( 'An invalid engine was passed to get_popular_search_details()', 'searchwp-metrics' )
				);
			}
		}

		$payload = array();

		$query = new QueryPopularQueriesOverTimeDetails( array(
			'after'  => $after,
			'before' => $before,
			'engine' => $engine,
			'limit'  => absint( $limit ),
		) );

		$popular_queries = $query->get_results();

		foreach ( $popular_queries as $popular_query ) {
			$clicks_for_query = new QueryPopularClicksOverTime( array(
				'after'               => $after,
				'before'              => $before,
				'engine'              => $engine,
				'limited_to_searches' => array( $popular_query->id ),
			) );

			$clicks_for_query_details = $clicks_for_query->get_results();
			$clicks = array();

			foreach ( $clicks_for_query_details as $clicks_for_query_detail ) {
				$clicks[] = array(
					'post_id'    => absint( $clicks_for_query_detail->post_id ),
					'post_title' => $clicks_for_query_detail->post_title,
					'clicks'     => absint( $clicks_for_query_detail->clicks ),
					'permalink'  => get_permalink( $clicks_for_query_detail->post_id ),
				);
			}

			$payload[] = array(
				'query'  => $popular_query,
				'clicks' => $clicks,
			);
		}

		wp_send_json_success( $payload );
	}

	/**
	 * Callback for query limiter multiselect that searches search queries for an exact match
	 */
	function find_search_queries() {
		global $wpdb;

		check_ajax_referer( 'searchwp_metrics_ajax' );

		$search_query = isset( $_REQUEST['searchquery'] ) ? $_REQUEST['searchquery'] : '';
		$search_query = strtolower( stripslashes( $search_query ) );

		$search = new QuerySearchSearchQueries( array(
			'query' => $search_query,
		) );

		$search->build_sql();
		$sql = $search->get_sql();
		$sql = $wpdb->prepare(
			$sql,
			$search_query
		);

		$payload = $wpdb->get_results( $sql );

		wp_send_json_success( $payload );
	}

	/**
	 * Setter for after property
	 */
	function set_after( $after ) {
		$this->after = $after;
	}

	/**
	 * Setter for before property
	 */
	function set_before( $before ) {
		$this->before = $before;
	}

	/**
	 * Setter for engine property
	 */
	function set_engine( $engine ) {
		$this->engine = $engine;
	}

	/**
	 * Adds a query to the local user metadata to ensure it's ignored in Metrics
	 */
	function add_ignored_query() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		if ( ! class_exists( 'SearchWP_Stats' ) || ! defined( 'SEARCHWP_PREFIX' ) ) {

			// SearchWP 4 compatibility.
			if ( class_exists( '\\SearchWP\\Statistics' ) && method_exists( '\\SearchWP\\Statistics', 'ignore_query' ) ) {
				$query_to_ignore = isset( $_REQUEST['query'] ) ? stripslashes( $_REQUEST['query'] ) : '';

				if ( empty( $query_to_ignore ) ) {
					wp_send_json_error();
				}

				\SearchWP\Statistics::ignore_query( $query_to_ignore );

				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}

		$searchwp_core_stats = new \SearchWP_Stats();

		// Query ignoring expects the query to be md5 hashed
		$query_to_ignore = isset( $_REQUEST['query'] ) ? md5( $_REQUEST['query'] ) : '';

		if ( empty( $query_to_ignore ) ) {
			wp_send_json_error();
		}

		// SearchWP core's ignoring depends on the query being present in the core log
		// table, but in Metrics that's not the case, so we're manually going
		// to add the hash to the usermeta and in doing so apply the ignore
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
		$ignored_queries = $searchwp_core_stats->get_ignored_queries( $user_id );

		if ( ! array_key_exists( $query_to_ignore, $ignored_queries ) ) {
			$ignored_queries[ $query_to_ignore ] = $query_to_ignore;
		}

		update_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );

		wp_send_json_success();
	}

	/**
	 * Removes a query to the local user metadata to ensure it's ignored in Metrics
	 */
	function remove_ignored_query() {
		global $wpdb;

		check_ajax_referer( 'searchwp_metrics_ajax' );

		if ( ! class_exists( 'SearchWP_Stats' ) || ! defined( 'SEARCHWP_PREFIX' ) ) {

			// SearchWP 4 compatibility.
			if ( class_exists( '\\SearchWP\\Statistics' ) && method_exists( '\\SearchWP\\Statistics', 'unignore_query' ) ) {
				$query_to_unignore = isset( $_REQUEST['hash'] ) ? stripslashes( $_REQUEST['hash'] ) : '';

				// Technical debt: the md5 hash is sent here so we need to reverse lookup. Sorry.
				$metrics = new \SearchWP_Metrics();
				$table   = $metrics->get_db_prefix() . 'queries';

				$query_to_unignore = $wpdb->get_var( $wpdb->prepare ( "
					SELECT query
					FROM {$table}
					WHERE md5(query) = %s
					LIMIT 1
				", $query_to_unignore ) );

				if ( empty( $query_to_unignore ) ) {
					wp_send_json_error();
				}

				\SearchWP\Statistics::unignore_query( $query_to_unignore );

				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}

		$searchwp_core_stats = new \SearchWP_Stats();

		// Query ignoring expects the query to be md5 hashed
		$query_to_remove = isset( $_REQUEST['hash'] ) ? $_REQUEST['hash'] : '';

		if ( empty( $query_to_remove ) ) {
			wp_send_json_error();
		}

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
		$ignored_queries = $searchwp_core_stats->get_ignored_queries( $user_id );

		if ( array_key_exists( $query_to_remove, $ignored_queries ) ) {
			unset( $ignored_queries[ $query_to_remove ] );
		}

		update_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );

		wp_send_json_success();
	}

	/**
	 * The main callback when retrieving metrics for a submitted date range
	 */
	function get_metrics() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		$this->after   = isset( $_REQUEST['after'] )   ? $_REQUEST['after'] : '30 days ago';
		$this->before  = isset( $_REQUEST['before'] )  ? $_REQUEST['before'] : 'now';
		$this->engines = isset( $_REQUEST['engines'] ) ? $_REQUEST['engines'] : array( 'default' );
		$this->limit   = isset( $_REQUEST['limit'] )   ? $_REQUEST['limit'] : 10;

		// Persist the chosen engines as a setting.
		$this->settings->set_option( 'last_engines', $this->engines );

		$searches_over_time        = $this->get_searches_over_time();
		$failed_searches_over_time = $this->get_failed_searches_over_time();
		$popular_queries_over_time = $this->get_popular_queries_over_time();
		$popular_clicks_over_time  = $this->get_popular_clicks_over_time();
		$ignored_queries           = $this->get_ignored_queries();
		$average_searches_per_user = $this->get_average_searches_per_user();
		$average_clicks_per_search = $this->get_average_clicks_per_search();
		$average_click_rank        = $this->get_average_click_rank();
		$total_clicks              = $this->get_total_clicks();

		wp_send_json_success( array(
			'searches_over_time'        => $searches_over_time,
			'failed_searches_over_time' => $failed_searches_over_time,
			'popular_queries_over_time' => $popular_queries_over_time,
			'popular_clicks_over_time'  => $popular_clicks_over_time,
			'ignored_queries'           => $ignored_queries,
			'average_searches_per_user' => $average_searches_per_user,
			'average_clicks_per_search' => $average_clicks_per_search,
			'average_click_rank'        => $average_click_rank,
			'total_clicks'              => $total_clicks,
		) );
	}

	/**
	 * Retrieves the total clicks for an engine during a time frame
	 */
	function get_total_clicks() {
		global $wpdb;

		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryTotalClicks( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
			) );

			$dataset = $query->get_results();
			$total_clicks = $wpdb->num_rows;

			$payload[ $engine ] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'statistic' => $total_clicks,
			);
		}

		return $payload;
	}

	/**
	 * Retrieves the average click rank for an engine during a time frame
	 */
	function get_average_click_rank() {
		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryAverageClickRank( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
			) );

			$dataset = $query->get_results();

			if ( ! empty( $dataset ) ) {
				$average_click_rank = wp_list_pluck( $dataset, 'average' );
				$formatted_stat = number_format_i18n( (float) $average_click_rank[0], 3 );
			} else {
				$formatted_stat = 0.00;
			}

			$payload[ $engine ] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'statistic' => $formatted_stat,
			);
		}

		return $payload;
	}

	/**
	 * Retrieves the average clicks per search (from users that have searched) for an engine during a time frame
	 */
	function get_average_clicks_per_search() {
		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryAverageClicksPerSearch( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
			) );

			$dataset = $query->get_results();

			if ( ! empty( $dataset ) ) {
				$clicks_per_search = wp_list_pluck( $dataset, 'clicks' );
				$total_clicks = array_sum( $clicks_per_search );

				if ( empty( $clicks_per_search ) ) {
					$average_clicks_per_search = 0;
				} else {
					$average_clicks_per_search = $total_clicks / count( $clicks_per_search );
				}

				$formatted_stat = number_format_i18n( (float) $average_clicks_per_search, 3 );
			} else {
				$formatted_stat = 0.00;
			}

			$payload[ $engine ] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'statistic' => $formatted_stat,
			);
		}

		return $payload;
	}

	/**
	 * Retrieves the average searches per user for an engine during a time frame
	 */
	function get_average_searches_per_user() {
		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryAverageSearchesPerUser( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
			) );

			$dataset = $query->get_results();

			if ( ! empty( $dataset ) ) {
				$uids = wp_list_pluck( $dataset, 'uid' );
				$uid_counts = array_count_values( $uids );
				$total_searches = array_sum( $uid_counts );

				if ( empty( $uid_counts ) ) {
					$average_searches_per_user = 0;
				} else {
					$average_searches_per_user = $total_searches / count( $uid_counts );
				}

				$formatted_stat = number_format_i18n( (float) $average_searches_per_user, 3 );
			} else {
				$formatted_stat = 0.00;
			}

			$payload[ $engine ] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'statistic' => $formatted_stat,
			);
		}

		return $payload;
	}

	/**
	 * Getter for ignored queries
	 */
	function get_ignored_queries() {
		check_ajax_referer( 'searchwp_metrics_ajax' );

		if ( ! $this->is_legacy_searchwp() ) {
			$ignored_queries = \SearchWP\Settings::get( 'ignored_queries', 'array' );

			$payload = [];
			if ( ! empty( $ignored_queries ) ) {
				foreach ( $ignored_queries as $ignored_query_string ) {
					$payload[] = array(
						'hash' => md5( $ignored_query_string ),
						'query' => $ignored_query_string,
					);
				}
			}

			return $payload;
		} else {
			if ( ! class_exists( 'SearchWP_Stats' ) ) {
				wp_send_json_error();
			}

			$searchwp_core_stats = new \SearchWP_Stats();
			$ignored_queries     = $searchwp_core_stats->get_ignored_queries();
		}

		// We're going to translate the ignored query hashes back to their original state because
		// ignored queries can be un-ignored within the UI
		$query = new QueryTranslateIgnoredQueries(array(
			'hashes' => $ignored_queries,
		));

		$ignored_query_strings = $query->get_results();

		$payload = [];
		if ( ! empty( $ignored_query_strings ) ) {
			foreach ( $ignored_query_strings as $ignored_query_string ) {
				$payload[] = array(
					'hash' => md5( $ignored_query_string->query ),
					'query' => $ignored_query_string->query,
				);
			}
		}

		return $payload;
	}

	/**
	 * Retrieves the engine label from the engine name
	 */
	function get_engine_label_from_name( $name = 'default' ) {
		if ( ! $this->is_legacy_searchwp() ) {
			$engine_settings = \SearchWP\Settings::get_engine_settings( $name );
			return $engine_settings['label'];
		} else {
			if ( ! function_exists( 'SWP' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! SWP()->is_valid_engine( $name ) ) {
				return __( 'Invalid Engine', 'searchwp' );
			}

			$engines = searchwp_get_setting( 'engines' );

			if ( ! array_key_exists( $name, $engines ) ) {
				return __( 'Invalid Engine', 'searchwp' );
			}

			$label = isset( $engines[ $name ]['searchwp_engine_label'] ) ? $engines[ $name ]['searchwp_engine_label'] : __( 'Default', 'searchwp' );

			return $label;
		}
	}

	/**
	 * AJAX callback that retrieves the number of searches for each day within a date range
	 */
	function get_searches_over_time() {

		// This data will be prepped to be used directly by the charting library
		$chart_labels = array();
		$datasets = array();

		// We're always working with an array of engines
		foreach ( $this->engines as $engine ) {
			$query = new QuerySearchesOverTime( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
			) );

			$dataset = $query->get_results();

			$datasets[] = array(
				'engine' => $this->get_engine_label_from_name( $engine ),
				'dataset' => array_map( 'absint', array_values( wp_list_pluck( $dataset, 'searches' ) ) ),
			);

			// Labels need be defined only once
			$chart_labels = empty( $chart_labels ) ? $this->get_chart_labels_from_results( $dataset ) : $chart_labels;
		}

		$payload = array(
			'labels'   => $chart_labels,
			'datasets' => $datasets,
		);

		return $payload;
	}

	/**
	 * AJAX callback that retrieves common queries over time
	 */
	function get_popular_queries_over_time() {

		// The payload is going to be broken out per engine, each with a unique set of labels
		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryPopularQueriesOverTime( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
				'limit'     => $this->limit,
			) );

			$dataset = $query->get_results();

			$payload[] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'labels' => wp_list_pluck( $dataset, 'query' ),
				'dataset' => array_map( 'absint', wp_list_pluck( $dataset, 'searchcount' ) ),
			);
		}

		return $payload;
	}

	/**
	 * Retrieves the popdlar clicks for an engine during a time frame
	 */
	function get_popular_clicks_over_time() {

		// This data will be displayed on a Radar chart so we need to find a common set of labels for each dataset
		$chart_labels = array();
		$datasets = array();
		$payload = array();

		foreach ( $this->engines as $engine ) {

			$query = new QueryPopularClicksOverTime( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
				'limit'     => apply_filters( 'searchwp_metrics_popular_clicks_limit', 1500 ),
			) );

			$dataset = $query->get_results();

			if ( empty( $dataset ) ) {
				continue;
			}

			// So as to remain somewhat performant, we're going to determine the average number of clicks and use that as the minimum
			$click_records = wp_list_pluck( $dataset, 'clicks' );
			$avg_clicks = ceil( array_sum( $click_records ) / count( $click_records ) );

			foreach ( $dataset as $post ) {

				if ( absint( $post->clicks ) < $avg_clicks ) {
					continue;
				}

				$post_id = $post->post_id;

				$searches_for_post_id = $this->get_queries_for_post_ids( $post_id, $engine );
				// $searches_for_post_id is an array of objects with the following keys:
				//   - query (the search query used to retrieve that post)
				//   - count (the number of searches of that query)

				// We need to track all of the search queries used to find all posts
				// for this engine, for use as chart labels
				$chart_labels = array_merge( $chart_labels, wp_list_pluck( $searches_for_post_id, 'query' ) );

				$datasets[] = array(
					'label'     => get_the_title( $post_id ),
					'post_id'   => absint( $post_id ),
					'post_type' => get_post_type( $post_id ),
					'permalink' => get_permalink( $post_id ),
					'raw_data'  => $searches_for_post_id,
				);
			}

			// We need to determine how many posts were found per search query
			$chart_labels_counts = array_count_values( $chart_labels );
			// $chart_labels_counts is an array with keys of search queries and values of the number of times that search query was searched

			// Lastly we're going to make a unique list of labels for display in the chart
			$chart_labels = array_values( array_unique( $chart_labels ) );

			// Now that we've looped through all of the queries that resulted in these clicks
			// we need to reexamine the data for each dataset to ensure that counts are correct
			// because new labels have likely been added, so we need to fill those gaps
			$datasets = $this->process_datasets( $datasets, $chart_labels );
			$payload[] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'labels' => $chart_labels,
				'counts' => $chart_labels_counts,
				'insights' => array(
					'analysis'  => $this->get_click_count_analysis( $chart_labels_counts, $datasets ),
					'popular'   => $this->get_click_count_popular_content( $dataset ),
					'underdogs' => $this->get_click_count_underdogs( $engine ),
				),
				'dataset' => $datasets,
			);
		}

		return $payload;
	}

	/**
	 * Analyzes a dataset from popular_clicks_over_time to determine what content is getting many
	 * clicks despite a low click position, indicating content needs to be on-site-SEO'd
	 */
	function get_click_count_underdogs( $engine ) {
		if ( ! $this->is_legacy_searchwp() ) {
			if ( ! defined( 'SEARCHWP_VERSION' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! \SearchWP\Settings::get_engine_settings( $engine ) ) {
				wp_send_json_error(
					__( 'An invalid engine was passed to get_queries_for_post_id()', 'searchwp-metrics' )
				);
			}
		} else {
			if ( ! function_exists( 'SWP' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! SWP()->is_valid_engine( $engine ) ) {
				return new WP_Error( 'invalid_engine', __( 'An invalid engine was passed to get_queries_for_post_id()', 'searchwp-metrics' ) );
			}
		}

		$payload = array();

		$query = new QueryUnderdogs( array(
			'after'        => $this->after,
			'before'       => $this->before,
			'engine'       => $engine,
			'min_avg_rank' => 8,
		) );

		$dataset = $query->get_results();

		if ( empty( $dataset ) ) {
			$average_click_count = 0;
		} else {
			$average_click_count = array_sum( wp_list_pluck( $dataset, 'click_count' ) ) / count( $dataset );
		}

		$click_count_threshold = $average_click_count * floatval( apply_filters( 'searchwp_metrics_underdog_click_threshold', 1 ) );

		foreach ( $dataset as $underdog ) {
			$click_count = absint( $underdog->click_count );

			if ( $click_count < $click_count_threshold ) {
				continue;
			}

			$search_queries = new QueryQueriesForPostIds( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
				'limit'     => $this->limit,
				'post_ids'  => array( $underdog->post_id ),
			) );

			$payload[] = array(
				'post_id'     => $underdog->post_id,
				'post_title'  => $underdog->post_title,
				'click_count' => $click_count,
				'avg_rank'    => absint( $underdog->avg_rank ),
				'permalink'   => get_permalink( $underdog->post_id ),
				'queries'     => $search_queries->get_results(),
			);
		}

		return $payload;
	}

	/**
	 * Analyzes a dataset from popular_clicks_over_time to determine what content is most popular
	 * by comparing click through rates to the average click through rate of the overall set
	 */
	function get_click_count_popular_content( $dataset ) {
		$clicks = wp_list_pluck( $dataset, 'clicks' );
		if ( empty( $clicks ) ) {
			$average_clicks_per_post = 0;
		} else {
			$average_clicks_per_post = array_sum( $clicks ) / count( $clicks );
		}

		// Posts with a click rate greater than this threshold over the average click rate will be considered popular
		// Default is 4x the average clicks indicates something is popular
		$threshold = floatval( apply_filters( 'searchwp_metrics_popular_content_threshold', 4 ) );

		$popular_content = array();

		foreach ( $dataset as $result ) {
			if ( absint( $result->clicks ) >= ( $threshold * $average_clicks_per_post ) ) {
				$popular_content[] = array(
					'post_id' => $result->post_id,
					'post_title' => $result->post_title,
					'permalink' => get_permalink( $result->post_id ),
					'clicks' => $result->clicks,
				);
			}
		}

		return $popular_content;
	}

	/**
	 * Processes a raw dataset to fill in gaps in the data
	 */
	private function process_datasets( $datasets, $chart_labels ) {
		foreach ( $datasets as $key => $dataset ) {
			$data = array();

			foreach ( $chart_labels as $chart_label ) {
				$found_match = false;

				if ( empty( $dataset['raw_data'] ) ) {
					continue;
				}

				foreach ( $dataset['raw_data'] as $data_point ) {

					if ( $data_point->query === $chart_label ) {
						$data[] = absint( $data_point->count );
						$found_match = true;
						break;
					}
				}
				if ( ! $found_match ) {
					$data[] = 0;
				}
			}

			unset( $datasets[ $key ]['raw_data'] );
			$datasets[ $key ]['data'] = $data;
		}

		return $datasets;
	}

	/**
	 * Using a submitted dataset, determines which search phrases are generating too
	 * many clicks to too many results, indicating that content can be improved upon
	 */
	function get_click_count_analysis( $counts, $dataset ) {
		$notes = array();

		// This threshold defines the minimum number of separate posts that were clicked to indicate that
		// content can be improved upon e.g. there are too many potential search results for the search term
		$minimum_click_threshold = apply_filters( 'searchwp_metrics_minimum_click_warning_threshold', 4 );

		$i = -1;
		foreach ( $counts as $query => $click_count ) {
			$i++;

			// If this search query did not generate enough separate posts clicks, there's nothing else to do
			if ( $click_count < absint( $minimum_click_threshold ) ) {
				continue;
			}

			// We have a search query that's generating too many clicks (e.g. visitor not finding what they're looking for)
			$clicks = 0;
			$posts_that_were_clicked = array();
			foreach ( $dataset as $search_result ) {
				$these_clicks = $search_result['data'][ $i ];

				// This search result doesn't apply
				if ( empty( $these_clicks ) ) {
					continue;
				}

				$clicks += $these_clicks;

				$posts_that_were_clicked[] = array(
					'clicks' => $these_clicks,
					'post_id' => $search_result['post_id'],
					'post_type' => get_post_type( $search_result['post_id'] ),
					'permalink' => get_permalink( $search_result['post_id'] ),
					'post_title' => $search_result['label'],
				);
			}

			$notes[ $query ] = array(
				'query' => $query,
				'posts' => $posts_that_were_clicked,
				'clicks' => $clicks,
			);
		}

		return $notes;
	}

	/**
	 * Retrieves all of the search queries submitted that resulted in a click
	 * to any number of post IDs
	 */
	function get_queries_for_post_ids( $post_ids, $engine = 'default' ) {
		if ( ! $this->is_legacy_searchwp() ) {
			if ( ! defined( 'SEARCHWP_VERSION' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! \SearchWP\Settings::get_engine_settings( $engine ) ) {
				wp_send_json_error(
					__( 'An invalid engine was passed to get_queries_for_post_id()', 'searchwp-metrics' )
				);
			}
		} else {
			if ( ! function_exists( 'SWP' ) ) {
				wp_send_json_error(
					__( 'SearchWP must be activated', 'searchwp-metrics' )
				);
			}

			if ( ! SWP()->is_valid_engine( $engine ) ) {
				return new WP_Error( 'invalid_engine', __( 'An invalid engine was passed to get_queries_for_post_ids()', 'searchwp-metrics' ) );
			}
		}

		if ( ! is_array( $post_ids ) ) {
			$post_ids = explode( ',', $post_ids );
		}

		$post_ids = array_map( 'absint', $post_ids );
		$post_ids = array_unique( $post_ids );

		$query = new QueryQueriesForPostIds( array(
			'after'     => $this->after,
			'before'    => $this->before,
			'engine'    => $engine,
			'limit'     => $this->limit,
			'post_ids'  => $post_ids,
		) );

		return $query->get_results();
	}

	/**
	 * Formats chart labels into the date format we want
	 */
	function get_chart_labels_from_results( $results ) {
		return array_map( function( $date ) {
			return date_i18n( 'M j', strtotime( $date ) );
		}, array_keys( $results ) );
	}

	/**
	 * AJAX callback that retrieves failed searches over time
	 */
	function get_failed_searches_over_time() {
		// The payload is going to be broken out per engine, each with a unique set of labels
		$payload = array();

		foreach ( $this->engines as $engine ) {
			$query = new QueryFailedSearchesOverTime( array(
				'after'     => $this->after,
				'before'    => $this->before,
				'engine'    => $engine,
				'limit'     => $this->limit * 100,
			) );

			$dataset = $query->get_results();

			$payload[] = array(
				'engine' => $engine,
				'engineLabel' => $this->get_engine_label_from_name( $engine ),
				'labels' => wp_list_pluck( $dataset, 'query' ),
				'dataset' => array_map( 'absint', wp_list_pluck( $dataset, 'failcount' ) ),
			);
		}

		return $payload;
	}
}
