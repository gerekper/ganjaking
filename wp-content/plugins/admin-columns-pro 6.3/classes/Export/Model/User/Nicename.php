<?php

namespace ACP\Export\Model\User;

use ACP\Export\Service;

class Nicename implements Service {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return $user->user_nicename ?? '';
	}

}