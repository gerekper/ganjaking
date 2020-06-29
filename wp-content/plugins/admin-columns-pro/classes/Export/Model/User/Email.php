<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * Email (default column) exportability model
 * @since 4.1
 */
class Email extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return isset( $user->user_email ) ? $user->user_email : '';
	}

}