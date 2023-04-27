<?php
declare( strict_types=1 );

namespace ACP\ListScreenFactory;

use AC;
use ACP\ListScreen\Post;

class PostFactory extends AC\ListScreenFactory\PostFactory {

	protected function create_list_screen( string $key ): AC\ListScreen\Post {
		return new Post( $key );
	}

}