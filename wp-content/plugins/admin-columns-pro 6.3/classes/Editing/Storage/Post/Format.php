<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;
use RuntimeException;

class Format implements Storage {

	public function get( int $id ) {
		return get_post_format( $id );
	}

	public function update( int $id, $data ): bool {
		$result = set_post_format( $id, $data );

		if ( $result && is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return true;
	}

}