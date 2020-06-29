<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Parent (default column) exportability model
 * @since 4.1
 */
class PostParent extends Model {

	public function get_value( $id ) {
		return get_the_title( wp_get_post_parent_id( $id ) );
	}

}