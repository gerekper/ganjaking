<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class Description extends Model {

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	public function save( $id, $value ) {
		return false !== update_user_meta( $id, 'description', $value );
	}

}