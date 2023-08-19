<?php

namespace ACA\Types\Filtering;

use ACA\Types\Column;
use ACP;

/**
 * @property Column $column
 */
class Checkbox extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'options'      => [],
			'empty_option' => true,
		];
	}

}