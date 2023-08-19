<?php

namespace ACP\Helper\Select\Value;

use AC;

final class Comment
	implements AC\Helper\Select\Value {

	public function get_value( $comment ) {
		return $comment->comment_ID;
	}

}