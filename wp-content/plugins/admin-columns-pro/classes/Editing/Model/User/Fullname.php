<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class Fullname extends Model {

	public function get_edit_value( $id ) {
		return [
			'first_name' => get_user_meta( $id, 'first_name', true ),
			'last_name'  => get_user_meta( $id, 'last_name', true ),
		];
	}

	public function get_view_settings() {
		return [
			'type'                   => 'fullname',
			'placeholder_first_name' => __( 'First Name', 'codepress-admin-columns' ),
			'placeholder_last_name'  => __( 'Last Name', 'codepress-admin-columns' ),
		];
	}

	public function save( $id, $value ) {
		if ( isset( $value['first_name'] ) ) {
			update_user_meta( $id, 'first_name', $value['first_name'] );
		}

		if ( isset( $value['last_name'] ) ) {
			update_user_meta( $id, 'last_name', $value['last_name'] );
		}

		return true;
	}

}