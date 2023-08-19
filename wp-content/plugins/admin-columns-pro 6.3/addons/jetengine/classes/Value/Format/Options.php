<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Field;
use ACA\JetEngine\Value\Formatter;

class Options extends Formatter {

	public function format( $raw_value ): ?string {
		if ( ! $raw_value ) {
			return $this->column->get_separator();
		}

		$options = $this->field instanceof Field\Options ? $this->field->get_options() : [];

		return $options[ $raw_value ] ?? $raw_value;
	}

}