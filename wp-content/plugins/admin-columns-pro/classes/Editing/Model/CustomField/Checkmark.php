<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;

class Checkmark extends Model\CustomField {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'0' => __( 'False', 'codepress-admin-columns' ),
				'1' => __( 'True', 'codepress-admin-columns' ),
			],
		];
	}

}