<?php

namespace ACP\Editing\Model\Media;

use ACP\Editing\Model;

class Date extends Model\Post\Date {

	public function get_edit_value( $id ) {
		$post = get_post( $id );

		if ( ! $post ) {
			return null;
		}

		return $post->post_date;
	}

}