<?php

namespace ACA\WC\Filtering;

use ACP;

class MetaWithoutEmptyOption extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$data = parent::get_filtering_data();
		$data['empty_option'] = false;

		return $data;
	}

}