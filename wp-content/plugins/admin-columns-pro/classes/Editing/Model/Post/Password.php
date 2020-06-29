<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Password extends Model\Post {

	public function get_edit_value( $id ) {
		return get_post_field( 'post_password', $id );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_password' => $value ] );
	}

}