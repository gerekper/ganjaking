<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;
use WP_Comment;

class Comment extends Model\Comment {

	public function get_edit_value( $id ) {
		$comment = get_comment( $id );

		if ( ! $comment instanceof WP_Comment ) {
			return false;
		}

		return $comment->comment_content;
	}

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	public function save( $id, $value ) {
		return $this->update_comment( $id, [ 'comment_content' => $value ] );
	}

}