<?php

namespace ACP\Filtering\Strategy;

use ACP\Filtering\Strategy;
use WP_Comment_Query;

class Comment extends Strategy {

	public function handle_request() {
		add_action( 'pre_get_comments', [ $this, 'handle_filter_requests' ], 2 );
	}

	/**
	 * Handle filter request for single values
	 *
	 * @param WP_Comment_Query $comment_query
	 *
	 * @since 3.5
	 */
	public function handle_filter_requests( $comment_query ) {
		// meta_query in WP_Comment is by default a string. We parse it to an array because of PHP7.1 compatibility issue
		$comment_query->query_vars['meta_query'] = wp_parse_args( $comment_query->query_vars['meta_query'] );
		$comment_query->query_vars = $this->model->get_filtering_vars( $comment_query->query_vars );
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 * @since 3.5
	 */
	public function get_values_by_db_field( $field ) {
		global $wpdb;

		$results = $wpdb->get_col( "
			SELECT " . sanitize_key( $field ) . "
			FROM {$wpdb->comments} AS c
			INNER JOIN {$wpdb->posts} ps ON ps.ID = c.comment_post_ID
			WHERE c." . sanitize_key( $field ) . " <> ''
		" );

		if ( ! $results ) {
			return [];
		}

		return $results;
	}

}