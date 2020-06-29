<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Model;

/**
 * Date (default column) exportability model
 * @since 4.1
 */
class Date extends Model {

	public function get_value( $id ) {
		return get_comment( $id )->comment_date;
	}

}