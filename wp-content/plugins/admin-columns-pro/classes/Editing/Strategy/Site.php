<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\RequestHandler;
use ACP\Editing\Strategy;

class Site implements Strategy {

	public function user_can_edit() {
		return current_user_can( 'manage_sites' );
	}

	public function user_can_edit_item( $id ) {
		return $this->user_can_edit();
	}

	public function get_query_request_handler() {
		return new RequestHandler\Query\Nullable();
	}

}