<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;

class Color extends Model\CustomField {

	/**
	 * @return array
	 */
	public function get_view_settings() {
		return [
			'type' => 'color',
		];
	}

}