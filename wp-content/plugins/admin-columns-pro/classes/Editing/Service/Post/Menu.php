<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;

class Menu extends Service\Menu {

	/**
	 * @param int $id
	 *
	 * @return string|false
	 */
	protected function get_title( $id ) {
		return get_post_field( 'post_title', $id );
	}

}