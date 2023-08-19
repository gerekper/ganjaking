<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;

class Modified implements Storage {

	public function get( int $id ) {
		$post = get_post( $id );

		if ( ! $post ) {
			return false;
		}

		return $post->post_modified;
	}

	public function update( int $id, $data ): bool {
		global $wpdb;

		$date = (string) $data;

		$date_gmt = get_date_from_gmt( $date );

		$result_1 = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_modified = %s WHERE ID = %d", $date, $id ) );
		$result_2 = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_modified_gmt = %s WHERE ID = %d", $date_gmt, $id ) );

		clean_post_cache( $id );

		return $result_1 !== false && $result_2 !== false;
	}

}