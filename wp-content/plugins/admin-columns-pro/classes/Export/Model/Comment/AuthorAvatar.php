<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class AuthorAvatar extends Model {

	public function get_value( $id ) {
		return get_avatar_url( get_comment( $id ) );
	}

}