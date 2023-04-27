<?php

namespace ACA\BP\Editing\Strategy;

use ACA\BP\Editing\RequestHandler;
use ACP;

class Group implements ACP\Editing\Strategy {

	public function user_can_edit_item( int $id ): bool {
		return current_user_can( 'bp_moderate', $id );
	}

	public function user_can_edit(): bool {
		return current_user_can( 'bp_moderate' );
	}

	public function get_query_request_handler(): ACP\Editing\RequestHandler {
		return new RequestHandler\Query\Groups();
	}

}