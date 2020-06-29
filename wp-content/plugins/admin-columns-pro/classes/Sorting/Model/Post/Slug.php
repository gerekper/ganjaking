<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\Model;
use wpdb;

class Slug extends Model {

	public function get_sorting_vars() {
		add_filter( 'posts_fields', [ $this, 'posts_fields_callback' ] );

		$args = [
			'suppress_filters' => false,
			'fields'           => [],
		];

		$ids = [];

		foreach ( $this->strategy->get_results( $args ) as $post ) {
			$ids[ $post->ID ] = $post->post_name;

			wp_cache_delete( $post->ID, 'posts' );
		}

		return [
			'ids' => $this->sort( $ids ),
		];
	}

	/**
	 * Only return fields required for sorting
	 * @return string
	 * @global wpdb $wpdb
	 */
	public function posts_fields_callback() {
		global $wpdb;

		remove_filter( 'posts_fields', [ $this, __FUNCTION__ ] );

		return "$wpdb->posts.ID, $wpdb->posts.post_name";
	}

}