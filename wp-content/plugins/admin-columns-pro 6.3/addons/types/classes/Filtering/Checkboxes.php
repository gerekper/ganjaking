<?php

namespace ACA\Types\Filtering;

use ACA\Types\Filtering;

class Checkboxes extends Filtering {

	public function get_filtering_data() {
		$data = [];

		$options = $this->column->get_field()->get( 'options' );

		foreach ( $this->get_meta_values_unserialized() as $value => $title ) {
			if ( isset( $options[ $value ] ) ) {
				$data[ $options[ $value ]['set_value'] ] = $options[ $value ]['title'];
			}
		}

		return [
			'options' => $data,
		];
	}

}