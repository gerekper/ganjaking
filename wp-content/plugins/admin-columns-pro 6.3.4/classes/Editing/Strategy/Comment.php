<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class Comment implements Strategy {

	public function user_can_edit(): bool {
		return current_user_can( 'moderate_comments' );
	}

	public function user_can_edit_item( int $id ): bool {
		return $this->user_can_edit() && current_user_can( 'edit_comment', $id );
	}

	public function get_query_request_handler(): RequestHandler {
		return new RequestHandler\Query\Comment();
	}

}