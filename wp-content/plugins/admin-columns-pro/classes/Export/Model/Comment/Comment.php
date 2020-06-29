<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Model;

/**
 * Comment (default column) exportability model
 * @since 4.1
 */
class Comment extends Model {

	public function get_value( $id ) {
		return get_comment_text( $id );
	}

}