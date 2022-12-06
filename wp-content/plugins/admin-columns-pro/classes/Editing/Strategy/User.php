<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class User implements Strategy {

	public function user_can_edit() {
		return current_user_can( 'edit_users' );
	}

	public function user_can_edit_item( $id ) {
		return $this->user_can_edit() && current_user_can( 'edit_user', $id );
	}

	public function get_query_request_handler() {
		return new RequestHandler\Query\User();
	}

}