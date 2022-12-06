<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Field;
use ACA\Pods\Filtering;

class PickUsers extends Filtering {

	public function get_filtering_data() {
		$field = $this->column->get_field();

		if ( ! $field instanceof Field\Pick\User ) {
			return [];
		}

		$options = $this->get_meta_values();

		return [
			'options'      => $field->get_users( $options ),
			'empty_option' => true,
		];
	}

}