<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class Taxonomy implements Strategy {

	public function user_can_edit(): bool {
		return current_user_can( 'manage_categories' );
	}

	public function user_can_edit_item( int $id ): bool {
		return $this->user_can_edit() && current_user_can( 'edit_term', $id );
	}

	public function get_query_request_handler(): RequestHandler {
		return new RequestHandler\Query\Taxonomy();
	}

}