<?php

namespace SearchWP_Metrics\Events;

use SearchWP_Metrics\Search;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Clicks
 * @package SearchWP_Metrics
 */
class Clicks {

	private $url_param = 'swpmtx';
	private $active = array();

	/**
	 * Clicks constructor.
	 */
	function __construct() {
		add_action( 'wp', array( $this, 'maybe_track_click' ) );
	}

	/**
	 * Adds a click tracker for a Search
	 *
	 * @param Search $search
	 */
	function add( Search $search ) {
		$this->url_param = sanitize_key( apply_filters( 'searchwp_metrics_click_param', $this->url_param ) );
		$this->active[]  = new Click( $search, $this->url_param );
	}

	/**
	 * Determines whether a click hash has already been recorded
	 *
	 * NOTE: This only applies for duplicate clicks on the same search, if the same user
	 * performs the same search and clicks the same link, it WILL be recorded
	 */
	function is_new_click( $hash, $post_id ) {
		global $wpdb;

		$metrics = new \SearchWP_Metrics();

		$clicks_table = $metrics->get_table_name( 'clicks' );

		$id = $wpdb->get_var( $wpdb->prepare(
			"SELECT id
			FROM $clicks_table
			WHERE hash = %d
			AND post_id = %d
			LIMIT 1",
			absint( $hash ),
			absint( $post_id )
		) );

		return empty( $id );
	}

	/**
	 * On wp_loaded, check to see if we should track a click (and potentially strip the URL params)
	 */
	function maybe_track_click() {
		global $wpdb, $post;

		$this->url_param = sanitize_key( apply_filters( 'searchwp_metrics_click_param', $this->url_param ) );

		if (
			! isset( $_REQUEST[ $this->url_param ] )
			|| empty( $_REQUEST[ $this->url_param ] )
			|| ! isset( $_REQUEST[ $this->url_param . 'nonce' ] )
			|| ! wp_verify_nonce( $_REQUEST[ $this->url_param . 'nonce' ], $this->url_param . $_REQUEST[ $this->url_param ] )
		) {
			return;
		}

		$metrics = new \SearchWP_Metrics();

		if ( $metrics->is_user_blocklisted() ) {
			// Short circuit this and Core
			return false;
		}

		$hashes     = new \SearchWP_Metrics\ID( 'hash' );
		$hash_id    = $hashes->get_numeric_id_from_hash( $_REQUEST[ $this->url_param ] );
		$position   = $hashes->get_serp_position_from_hash_id( $hash_id, $post->ID );

		$args = array(
			'tstamp'    => current_time( 'mysql', 1 ),
			'hash'      => absint( $hash_id ),
			'position'  => absint( $position ),
			'post_id'   => absint( $post->ID ),
		);

		$args = apply_filters( 'searchwp_metrics_click_args', $args );

		// To prevent "spam" from multiple repeated clicks, we need to verify that this has not been added
		if ( $this->is_new_click( $hash_id, $post->ID ) ) {

			// Track the click!
			if ( ! empty( $position ) ) {
				$wpdb->insert(
					$metrics->get_table_name( 'clicks' ),
					$args,
					array(
						'%s',
						'%d',
						'%d',
						'%d',
					)
				);
			}

			do_action( 'searchwp_metrics_click', $args );
		}

		if ( apply_filters( 'searchwp_metrics_redirect_tracking', true ) ) {
			$permalink = remove_query_arg(
				array( $this->url_param, $this->url_param . 'nonce' )
			);

			$redirect_status = apply_filters( 'searchwp_metrics_redirect_status', 302 );
			wp_safe_redirect( esc_url_raw( $permalink ), $redirect_status );

			die();
		}

	}

}
