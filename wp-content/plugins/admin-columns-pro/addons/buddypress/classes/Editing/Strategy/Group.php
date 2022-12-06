<?php

namespace ACA\BP\Editing\Strategy;

use ACA\BP\Editing\RequestHandler;
use ACP;

class Group implements ACP\Editing\Strategy {
	
	/**
	 * @param int|object $entry_id
	 *
	 * @return bool|int
	 */
	public function user_can_edit_item( $entry_id ) {
		if ( is_object( $entry_id ) ) {
			$entry_id = $entry_id->id;
		}

		return current_user_can( 'bp_moderate', $entry_id );
	}

	public function user_can_edit() {
		return current_user_can( 'bp_moderate' );
	}

	public function get_query_request_handler() {
		return new RequestHandler\Query\Groups();
	}

}