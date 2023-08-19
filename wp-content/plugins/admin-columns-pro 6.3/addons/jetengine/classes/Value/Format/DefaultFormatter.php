<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Value\Formatter;

class DefaultFormatter extends Formatter {

	public function format( $raw_value ): ?string {
		if ( ! $raw_value ) {
			return $this->column->get_separator();
		}

		return $this->column->get_formatted_value( $raw_value, $raw_value );
	}

}