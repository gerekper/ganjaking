<?php

namespace ACP\Export\Model\User;

use ACP\Export\Service;

class Login implements Service {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return $user->user_login ?? '';
	}

}