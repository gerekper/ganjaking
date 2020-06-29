<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

class Roles extends Model {

	public function get_value( $id ) {
		return implode( ',', ac_helper()->user->get_role_names( $this->get_column()->get_raw_value( $id ) ) );
	}

}