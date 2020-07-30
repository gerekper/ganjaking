<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class Login extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return isset( $user->user_login ) ? $user->user_login : '';
	}

}