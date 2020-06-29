<?php

namespace ACP\Editing\Ajax\EditableRows;

use ACP\Editing\Ajax\EditableRows;
use WP_Query;

final class Post extends EditableRows {

	public function register() {
		add_action( 'pre_get_posts', [ $this, 'set_query_vars' ], PHP_INT_MAX - 100 );
		add_action( 'the_posts', [ $this, 'send_editable_rows' ], 10, 2 );
	}

	/**
	 * @param array    $posts
	 * @param WP_Query $query
	 */
	public function send_editable_rows( $posts, WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$editable_rows = [];

		foreach ( $posts as $post ) {
			if ( $this->strategy->user_has_write_permission( $post ) ) {
				$editable_rows[] = $post->ID;
			}
		}

		$this->success( $editable_rows );
	}

	/**
	 * @param WP_Query $query
	 */
	public function set_query_vars( WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$this->check_nonce();

		$per_page = $this->get_editable_rows_per_iteration();

		$query->set( 'nopaging', false );
		$query->set( 'offset', $this->get_offset() );
		$query->set( 'posts_per_page', $per_page );
		$query->set( 'posts_per_archive_page', $per_page );
		$query->set( 'fields', 'all' );
		$query->set( 'suppress_filters', false );
	}

}