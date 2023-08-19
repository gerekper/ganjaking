<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class Site implements Strategy {

	public function user_can_edit(): bool {
		return current_user_can( 'manage_sites' );
	}

	public function user_can_edit_item( int $id ): bool {
		return $this->user_can_edit();
	}

	public function get_query_request_handler(): RequestHandler {
		return new RequestHandler\Query\Nullable();
	}

}