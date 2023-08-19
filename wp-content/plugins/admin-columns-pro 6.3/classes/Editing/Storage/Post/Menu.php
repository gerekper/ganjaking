<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing;

class Menu extends Editing\Storage\Menu {

	protected function get_title( int $id ): string {
		return get_post_field( 'post_title', $id );
	}

}