<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Author implements Service {

	public function get_value( $id ) {
		$user = get_userdata( get_post_field( 'post_author', $id ) );

		return $user->display_name ?? '';
	}

}