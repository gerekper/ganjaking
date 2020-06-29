<?php

namespace ACP\Filtering\Model\Media;

use ACP\Filtering\Model;
use WP_Query;

class PostType extends Model {

	public function filter_by_post_type( $where, WP_Query $query ) {
		global $wpdb;

		$sub_query = $wpdb->prepare( "SELECT ID from {$wpdb->posts} WHERE post_type = %s", $this->get_filter_value() );

		if ( $query->is_main_query() ) {
			$where .= " AND {$wpdb->posts}.post_parent IN({$sub_query})";
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_post_type' ], 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		$parents = $this->strategy->get_values_by_db_field( 'post_parent' );
		$post_types = [];

		foreach ( $parents as $post ) {
			$post_type_object = get_post_type_object( get_post_type( $post ) );

			if ( ! $post_type_object ) {
				continue;
			}

			$post_types[ get_post_type( $post ) ] = $post_type_object->label;
		}

		unset( $post_types['attachment'] );

		return [
			'options' => $post_types,
		];
	}

}