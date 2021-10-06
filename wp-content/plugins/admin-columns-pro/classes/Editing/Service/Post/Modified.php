<?php

namespace ACP\Editing\Service\Post;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\View;

class Modified implements Service {

	public function get_view( $context ) {
		return new View\DateTime();
	}

	public function get_value( $id ) {
		$post = get_post( $id );

		if ( ! $post ) {
			return false;
		}

		return $post->post_modified;
	}

	public function update( Request $request ) {
		global $wpdb;

		$id = (int) $request->get( 'id' );
		$date = (string) $request->get( 'value' );

		$date_gmt = get_date_from_gmt( $date );

		$result_1 = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_modified = %s WHERE ID = %d", $date, $id ) );
		$result_2 = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_modified_gmt = %s WHERE ID = %d", $date_gmt, $id ) );

		clean_post_cache( $id );

		return $result_1 !== false && $result_2 !== false;
	}

}