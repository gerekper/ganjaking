<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Order extends Model\Post {

	public function get_view_settings() {
		return [
			'type' => 'number',
		];
	}

	public function get_edit_value( $id ) {
		return get_post_field( 'menu_order', $id );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'menu_order' => $value ] );
	}

}