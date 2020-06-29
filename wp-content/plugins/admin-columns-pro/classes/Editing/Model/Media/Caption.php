<?php

namespace ACP\Editing\Model\Media;

use ACP\Editing\Model;

class Caption extends Model\Post {

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	public function get_edit_value( $id ) {
		$value = parent::get_edit_value( $id );

		return $value ?: false;
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_excerpt' => $value ] );
	}

}