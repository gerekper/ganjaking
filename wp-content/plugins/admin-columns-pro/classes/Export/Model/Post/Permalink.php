<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Permalink column exportability model
 * @since 4.1
 */
class Permalink extends Model {

	public function get_value( $id ) {
		return get_permalink( $id );
	}

}