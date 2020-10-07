<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;
use ACP\Editing\Settings\BulkEditing;

class Nicename extends Model\User {

	public function get_edit_value( $id ) {
		return ac_helper()->user->get_user_field( 'user_nicename', $id );
	}

	public function get_view_settings() {
		return [
			self::VIEW_TYPE          => 'text',
			self::VIEW_REQUIRED      => true,
			self::VIEW_PLACEHOLDER   => $this->column->get_label(),
			self::VIEW_BULK_EDITABLE => false,
		];
	}

	public function save( $id, $value ) {
		return $this->update_user( $id, [ 'user_nicename' => $value ] );
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->remove_setting( BulkEditing::NAME);
	}

}