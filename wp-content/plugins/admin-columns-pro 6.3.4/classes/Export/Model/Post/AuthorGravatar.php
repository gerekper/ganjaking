<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class AuthorGravatar implements Service {

	public function get_value( $post_id ) {
		$author_id = ac_helper()->post->get_raw_field( 'post_author', $post_id );

		return get_avatar_url( $author_id );
	}

}