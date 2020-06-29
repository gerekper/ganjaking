<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * Author (default column) exportability model
 * @since 4.1
 */
class Author extends Model {

	public function get_value( $id ) {
		$user = get_userdata( get_post_field( 'post_author', $id ) );

		return isset( $user->display_name ) ? $user->display_name : '';
	}

}