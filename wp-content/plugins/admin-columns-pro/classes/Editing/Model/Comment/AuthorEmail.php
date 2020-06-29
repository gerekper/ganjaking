<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

class AuthorEmail extends Model\Comment {

	public function get_view_settings() {
		return [ 'type' => 'email' ];
	}

	public function save( $id, $value ) {
		return $this->update_comment( $id, [ 'comment_author_email' => $value ] );
	}

}