<?php

namespace SearchWP_CRO;

/**
 * Class Ajax
 */
class Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_searchwp_cro_get_search_results', array( $this, 'get_search_results' ) );
		add_action( 'wp_ajax_searchwp_cro_promote_result', array( $this, 'promote_result' ) );
		add_action( 'wp_ajax_searchwp_cro_release_result', array( $this, 'release_result' ) );
		add_action( 'wp_ajax_searchwp_cro_save_triggers', array( $this, 'save_triggers' ) );
		add_action( 'wp_ajax_searchwp_cro_clear_buoys', array( $this, 'clear_buoys' ) );
	}

	/**
	 * Returns whether the submitted engine name is valid.
	 *
	 * @param string $engine
	 * @return bool
	 */
	public function is_valid_engine( $engine ) {
		if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engine_settings = \SearchWP\Settings::get_engine_settings( $engine );

			return ! empty( $engine_settings );
		} else if ( function_exists( 'SWP' ) ) {
			return SWP()->is_valid_engine( $engine );
		}
	}

	/**
	 * Remove buoys
	 */
	public function clear_buoys() {
		global $wpdb;

		check_ajax_referer( 'searchwp-custom-results-order' );

		$query  = isset( $_REQUEST['query'] ) ? stripslashes( $_REQUEST['query'] ) : '';
		$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : '';

		if ( ! $this->is_valid_engine( $engine ) || empty( $query ) ) {
			wp_send_json_error();
		}

		$buoy_key = searchwp_cro_get_buoy_key( $query, $engine );

		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => $buoy_key ) );

		wp_send_json_success();
	}

	/**
	 * Promote a search result
	 */
	public function promote_result() {
		check_ajax_referer( 'searchwp-custom-results-order' );

		$post_id = isset( $_REQUEST['postId'] ) ? absint( $_REQUEST['postId'] ) : '';
		$query   = isset( $_REQUEST['query'] ) ? stripslashes( $_REQUEST['query'] ) : '';
		$engine  = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : '';

		if ( empty( $post_id ) || ! $this->is_valid_engine( $engine ) || empty( $query ) ) {
			wp_send_json_error();
		}

		$buoy_key = searchwp_cro_get_buoy_key( $query, $engine );

		// The existing promotions are returned as they would appear in search results.
		$existing_promotions = searchwp_cro_get_promoted( $query, $engine );

		if ( empty( $existing_promotions ) ) {
			$buoy = 1;
		} else {
			// We're going to promote this result to the top of the list by
			// incrementing the existing top-most buoy.
			$current_top_buoy = get_post_meta(
				$existing_promotions[0],
				$buoy_key,
				true
			);

			$buoy = absint( $current_top_buoy ) + 1;
		}

		update_post_meta( $post_id, $buoy_key, $buoy );

		wp_send_json_success();
	}

	/**
	 * Release a search result promotion
	 */
	public function release_result() {
		check_ajax_referer( 'searchwp-custom-results-order' );

		$post_id = isset( $_REQUEST['postId'] ) ? absint( $_REQUEST['postId'] ) : '';
		$query   = isset( $_REQUEST['query'] ) ? stripslashes( $_REQUEST['query'] ) : '';
		$engine  = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : '';

		if ( empty( $post_id ) || ! $this->is_valid_engine( $engine ) || empty( $query ) ) {
			wp_send_json_error();
		}

		$buoy_key = searchwp_cro_get_buoy_key( $query, $engine );

		delete_post_meta( $post_id, $buoy_key );

		wp_send_json_success();
	}

	/**
	 * Retrieve search results
	 */
	public function get_search_results() {
		check_ajax_referer( 'searchwp-custom-results-order' );

		$query  = isset( $_REQUEST['query'] ) ? stripslashes( $_REQUEST['query'] ) : '';
		$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : '';
		$page   = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;

		if ( ! $this->is_valid_engine( $engine ) || empty( $query ) || ! class_exists( 'SWP_Query' ) ) {
			wp_send_json_error();
		}

		$ppp = apply_filters( 'searchwp-cro-num-results', 20 );

		add_filter( 'searchwp_log_search', '__return_false' );
		add_filter( 'searchwp\statistics\log', '__return_false' );

		$results = new \SWP_Query( array(
			's'              => $query,
			'engine'         => $engine,
			'posts_per_page' => absint( $ppp ),
			'page'           => absint( $page ),
		) );

		$payload = array(
			'id'             => searchwp_cro_get_buoy_key( $query, $engine ),
			'query'          => $query,
			'engine'         => $engine,
			'results'        => $results->posts,
			'paged'          => $results->paged,
			'max_num_pages'  => $results->max_num_pages,
			'promoted'       => searchwp_cro_get_promoted( $query, $engine ),
		);

		wp_send_json_success( $payload );
	}

	/**
	 * Save triggers
	 */
	public function save_triggers() {
		check_ajax_referer( 'searchwp-custom-results-order' );

		$triggers = isset( $_REQUEST['triggers'] ) ? json_decode( stripslashes( $_REQUEST['triggers'] ) ) : array();

		$settings = array();

		foreach ( $triggers as $trigger ) {
			$query  = $trigger->query;
			$engine = $trigger->engine->name;
			$exact  = $trigger->exact;

			$settings[ searchwp_cro_get_buoy_key( $query, $engine ) ] = array(
				'query'  => $query,
				'engine' => $engine,
				'exact'  => $exact,
			);
		}

		update_option( 'searchwp_cro_settings', $settings );

		wp_send_json_success();
	}
}
