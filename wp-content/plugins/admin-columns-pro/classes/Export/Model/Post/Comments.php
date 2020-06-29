<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Comments (default column) exportability model
 * @since 4.1
 */
class Comments extends Model {

	public function get_value( $id ) {
		return wp_count_comments( $id )->total_comments;
	}

}