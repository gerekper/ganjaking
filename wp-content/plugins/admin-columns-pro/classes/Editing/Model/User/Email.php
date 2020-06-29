<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class Email extends Model\User {

	public function get_edit_value( $id ) {
		return ac_helper()->user->get_user_field( 'user_email', $id );
	}

	public function get_view_settings() {
		return [
			'type'                   => 'email',
			'required'               => true,
			'placeholder'            => $this->column->get_label(),
			self::VIEW_BULK_EDITABLE => false,
		];
	}

	public function save( $id, $value ) {
		return $this->update_user( $id, [ 'user_email' => $value ] );
	}

}