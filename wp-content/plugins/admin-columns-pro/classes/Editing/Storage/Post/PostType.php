<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;

class PostType implements Storage {

	public function get( int $id ) {
		return get_post_type( $id );
	}

	public function update( int $id, $data ): bool {
		return false !== set_post_type( $id, $data );
	}

}