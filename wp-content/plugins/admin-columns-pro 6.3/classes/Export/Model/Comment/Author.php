<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Service;

class Author implements Service {

	public function get_value( $id ) {
		return get_comment_author( $id );
	}

}