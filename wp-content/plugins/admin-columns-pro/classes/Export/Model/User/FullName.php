<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class FullName extends Model {

	public function get_value( $id ) {
		return ac_helper()->user->get_display_name( $id, 'full_name' );
	}

}