<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Link extends Model\CustomField {

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $value;
		}

		return [
			'options'      => $options,
			'empty_option' => true,
		];
	}

}