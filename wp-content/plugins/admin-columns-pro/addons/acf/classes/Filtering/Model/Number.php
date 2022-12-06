<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class Number extends ACP\Filtering\Model\Meta {

	public function get_data_type() {
		return 'numeric';
	}

	public function is_ranged() {
		return true;
	}

}