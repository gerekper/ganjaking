<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\OptionsFactory;

use AC\Helper\Select\Options;

class PostStatus {

	public function create( string $post_type ): Options {
		$status_count = (array) wp_count_posts( $post_type );

		// Filter statuses that have no posts
		$status_count = array_filter( $status_count );

		if ( empty( $status_count ) ) {
			return Options::create_from_array( [] );
		}

		$statuses = array_keys( $status_count );

		$stati = get_post_stati( [ 'internal' => 0 ], 'objects' );

		$options = [];
		foreach ( $stati as $status ) {
			if ( in_array( $status->name, $statuses, true ) ) {
				$options[ $status->name ] = $status->label;
			}
		}

		return Options::create_from_array( $options );
	}

}