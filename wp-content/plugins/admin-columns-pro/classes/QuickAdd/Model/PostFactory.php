<?php

namespace ACP\QuickAdd\Model;

use AC\ListScreen;
use ACP\ListScreen\Post;

class PostFactory implements ModelFactory {

	public function create( ListScreen $list_screen ) {
		return $list_screen instanceof Post
			? new Create\Post( $list_screen->get_post_type() )
			: null;
	}

}