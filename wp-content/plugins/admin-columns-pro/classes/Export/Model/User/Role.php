<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * Role (default column) exportability model
 * @since 4.1
 */
class Role extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return implode( ', ', ac_helper()->user->translate_roles( $user->roles ) );
	}

}