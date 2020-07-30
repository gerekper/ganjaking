<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

class Nicename extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return isset( $user->user_nicename ) ? $user->user_nicename : '';
	}

}