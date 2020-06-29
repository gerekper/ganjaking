<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class Comment extends TableRows {

	public function register() {
		add_action( 'parse_comment_query', [ $this, 'handle_request' ] );
	}

}