<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Service;

class Comment implements Service {

	public function get_value( $id ) {
		return get_comment_text( $id );
	}

}