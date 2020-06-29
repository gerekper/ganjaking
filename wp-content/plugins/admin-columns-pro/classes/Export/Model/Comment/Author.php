<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Model;

/**
 * Author (default column) exportability model
 * @since 4.1
 */
class Author extends Model {

	public function get_value( $id ) {
		return get_comment_author( $id );
	}

}