<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;

class FeaturedImage implements Storage {

	public function get( int $id ) {
		return get_post_thumbnail_id( $id );
	}

	public function update( int $id, $data ): bool {
		return $data
			? (bool) set_post_thumbnail( $id, (int) $data )
			: delete_post_thumbnail( $id );
	}

}