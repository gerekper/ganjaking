<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Menu extends Model\Menu {

	/**
	 * @param int $id
	 *
	 * @return string|false
	 */
	protected function get_title( $id ) {
		return get_post_field( 'post_title', $id );
	}

}