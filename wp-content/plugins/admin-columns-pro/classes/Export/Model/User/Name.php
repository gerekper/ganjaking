<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * Display name (default column) exportability model
 * @since 4.1
 */
class Name extends Model {

	public function get_value( $id ) {
		$user = get_userdata( $id );

		return "{$user->first_name} {$user->last_name}";
	}

}