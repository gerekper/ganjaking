<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;

class Textarea extends Model\CustomField {

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

}