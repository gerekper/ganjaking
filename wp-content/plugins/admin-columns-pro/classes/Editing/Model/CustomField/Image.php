<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;

class Image extends Model\CustomField {

	public function get_view_settings() {
		$data = parent::get_view_settings();
		$data['type'] = 'media';
		$data['attachment']['library']['type'] = 'image';

		return $data;
	}
}