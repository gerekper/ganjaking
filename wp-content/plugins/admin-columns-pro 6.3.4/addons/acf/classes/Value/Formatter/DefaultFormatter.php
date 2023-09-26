<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Field\ValueWrapper;
use ACA\ACF\Value\Formatter;

class DefaultFormatter extends Formatter {

	public function format( $value, $id = null ) {
		if ( empty( $value ) && ! is_numeric( $value ) ) {
			return $this->column->get_empty_char();
		}

		$value = $this->column->get_formatted_value( $value, $value );
		$field = $this->column->get_field();

		if ( $value && $field instanceof ValueWrapper ) {
			$value = sprintf(
				'%s%s%s',
				$field->get_prepend(),
				$value,
				$field->get_append()
			);
		}

		return $value;
	}

}