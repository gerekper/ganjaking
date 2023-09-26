<?php

namespace ACA\Types\Filtering;

use ACA\Types\Filtering;

class Select extends Filtering {

	public function get_filtering_data() {
		$options = [];
		$data = [];

		foreach ( $this->column->get_field()->get( 'options' ) as $option ) {
			if ( ! is_array( $option ) ) {
				continue;
			}

			$data[ $option['value'] ] = $option['title'];
		}

		foreach ( $this->get_meta_values() as $value ) {
			if ( isset( $data[ $value ] ) ) {
				$options[ $value ] = $data[ $value ];
			}
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}