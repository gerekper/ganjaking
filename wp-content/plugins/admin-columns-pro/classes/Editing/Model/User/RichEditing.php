<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class RichEditing extends Model {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'true'  => __( 'True', 'codepress-admin-columns' ),
				'false' => __( 'False', 'codepress-admin-columns' ),
			],
		];
	}

	public function save( $id, $value ) {
		return false !== update_user_meta( $id, 'rich_editing', $value );
	}

}