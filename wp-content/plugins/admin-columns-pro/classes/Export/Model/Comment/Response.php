<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Service;

class Response implements Service {

	public function get_value( $id ) {
		$comment = get_comment( $id );

		if ( ! $comment->comment_post_ID ) {
			return '';
		}

		return get_the_title( $comment->comment_post_ID );
	}

}