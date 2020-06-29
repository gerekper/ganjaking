<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Post title (default column) exportability model
 * @since 4.1
 */
class Title extends Model {

	public function get_value( $id ) {
		return get_the_title( $id );
	}

}