<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Field;
use ACA\JetEngine\Value\Formatter;

class MultipleOptions extends Formatter {

	public function format( $raw_value ): ?string {
		if ( empty( $raw_value ) || ! is_array( $raw_value ) ) {
			return $this->column->get_separator();
		}

		$options = $this->field instanceof Field\Options ? $this->field->get_options() : [];
		$results = [];

		foreach ( $raw_value as $key ) {
			$results[] = $options[ $key ] ?? $key;
		}

		$setting_limit = $this->column->get_setting( 'number_of_items' );

		return ac_helper()->html->more( array_filter( $results ), $setting_limit ? $setting_limit->get_value() : false );
	}

}