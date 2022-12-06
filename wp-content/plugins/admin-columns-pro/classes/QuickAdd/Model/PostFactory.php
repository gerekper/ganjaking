<?php

namespace ACP\QuickAdd\Model;

use AC\ListScreen;
use ACP\ListScreen\Post;

class PostFactory implements ModelFactory {

	public function create( ListScreen $list_screen ) {
		$post_type = $list_screen instanceof Post
			? $list_screen->get_post_type()
			: null;

		return $post_type && post_type_exists( $post_type )
			? new Create\Post( $post_type )
			: null;
	}

}