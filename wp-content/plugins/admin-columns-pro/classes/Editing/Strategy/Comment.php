<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\Strategy;
use WP_Comment;

class Comment implements Strategy {

	/**
	 * @param $comment
	 *
	 * @return bool
	 * @since 4.0
	 */
	public function user_has_write_permission( $comment ) {
		if ( ! $comment instanceof WP_Comment ) {
			$comment = get_comment( $comment );

			if ( ! $comment instanceof WP_Comment ) {
				return false;
			}
		}

		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return false;
		}

		return true;
	}

}