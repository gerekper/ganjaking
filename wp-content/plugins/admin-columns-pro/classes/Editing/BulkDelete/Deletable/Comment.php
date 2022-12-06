<?php

namespace ACP\Editing\BulkDelete\Deletable;

use ACP\Editing\BulkDelete;
use ACP\Editing\BulkDelete\Deletable;
use ACP\Editing\RequestHandler;

class Comment implements Deletable {

	public function get_delete_request_handler(): RequestHandler {
		return new BulkDelete\RequestHandler\Comment();
	}

	public function user_can_delete(): bool {
		return current_user_can( 'moderate_comments' );
	}

	public function get_query_request_handler(): RequestHandler {
		return new RequestHandler\Query\Comment();
	}

}