<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Model;

/**
 * Response (default column) exportability model
 * @since 4.1
 */
class Response extends Model {

	public function get_value( $id ) {
		$comment = get_comment( $id );

		if ( ! $comment->comment_post_ID ) {
			return '';
		}

		return get_the_title( $comment->comment_post_ID );
	}

}