<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class Url extends Model\User {

	public function get_edit_value( $id ) {
		return ac_helper()->user->get_user_field( 'user_url', $id );
	}

	public function get_view_settings() {
		return [
			'type'        => 'text',
			'placeholder' => $this->column->get_label(),
		];
	}

	public function save( $id, $value ) {
		return $this->update_user( $id, [ 'user_url' => $value ] );
	}

}