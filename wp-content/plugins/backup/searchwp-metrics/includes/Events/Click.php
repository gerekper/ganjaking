<?php

namespace SearchWP_Metrics\Events;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Click
 * @package SearchWP_Metrics
 */
class Click {

	private $search;
	private $url_param;
	private $loop_starts = 0;
	private $loop_ends = 0;

	/**
	 * Click constructor.
	 *
	 * @param \SearchWP_Metrics\Search $search
	 * @param $url_param
	 */
	function __construct( \SearchWP_Metrics\Search $search, $url_param ) {
		$this->search    = $search;
		$this->url_param = $url_param;

		add_filter( 'post_link',       array( $this, 'the_permalink' ) );
		add_filter( 'page_link',       array( $this, 'the_permalink' ) );
		add_filter( 'post_type_link',  array( $this, 'the_permalink' ) );
		add_filter( 'attachment_link', array( $this, 'the_permalink' ) );
		add_filter( 'the_permalink',   array( $this, 'the_permalink' ) );

		add_action( 'loop_start', function() {
			$this->loop_starts++;
		});

		add_action( 'loop_end', function() {
			$this->loop_ends++;
		});
	}

	/**
	 * Adds tracking parameter to permalinks
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function the_permalink( $url ) {
		if ( ! $this->is_search_results_loop() && ! $this->doing_link_tracking() ) {
			return $url;
		}

		$tracked_permalink = add_query_arg(
			array(
				$this->url_param           => $this->search->get_query_hash(),
				$this->url_param . 'nonce' => wp_create_nonce( $this->url_param . $this->search->get_query_hash() )
			),
			$url
		);

		return apply_filters( 'searchwp_metrics_tracked_permalink', $tracked_permalink );
	}

	/**
	 * Whether we're in The Loop for a search results page
	 *
	 * @return bool
	 */
	function is_search_results_loop() {
		// This check accounts for circumstances where multiple Loops are on the page
		return is_main_query()
			&& (
				is_search()
				||
				( did_action( 'searchwp_after_query_index' ) || did_action( 'searchwp\query\before' ) )
			)
			&& $this->loop_starts > $this->loop_ends;
	}

	/**
	 * Check for manual link tracking actions
	 *
	 * @return bool
	 */
	function doing_link_tracking() {
		return did_action( 'searchwp_metrics_click_tracking_start' ) && ! did_action( 'searchwp_metrics_click_tracking_stop' );
	}
}
