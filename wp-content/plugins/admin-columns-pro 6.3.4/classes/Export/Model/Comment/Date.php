<?php

namespace ACP\Export\Model\Comment;

use ACP\Export\Service;

class Date implements Service {

	public function get_value( $id ) {
		return get_comment( $id )->comment_date;
	}

}