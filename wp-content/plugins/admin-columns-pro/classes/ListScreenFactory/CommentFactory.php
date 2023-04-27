<?php
declare( strict_types=1 );

namespace ACP\ListScreenFactory;

use AC;
use ACP\ListScreen\Comment;

class CommentFactory extends AC\ListScreenFactory\CommentFactory {

	protected function create_list_screen(): AC\ListScreen\Comment {
		return new Comment();
	}

}