<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Post extends Model\CustomField {

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $this->column->get_setting( 'post' )->format( $value, $value );
		}

		return [
			'options'      => $options,
			'empty_option' => true,
		];
	}

}