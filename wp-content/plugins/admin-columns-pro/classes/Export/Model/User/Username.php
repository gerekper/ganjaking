<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * Username (default column) exportability model
 * @since 4.1
 */
class Username extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return isset( $user->user_login ) ? $user->user_login : '';
	}

}