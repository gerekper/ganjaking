<?php

namespace ACP\Export\Model\User;

use ACP\Export\Model;

/**
 * Posts count (default column) exportability model
 * @since 4.1
 */
class Posts extends Model {

	public function get_value( $id ) {
		return count_user_posts( $id );
	}

}