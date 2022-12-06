<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;

class FieldsetText extends Column implements Formattable {

	use ConditionalFormatTrait;

	public function format_single_value( $values, $id = null ) {
		if ( ! $values ) {
			return $this->get_empty_char();
		}

		$results = [];
		foreach ( (array) $values as $label => $value ) {
			if ( ! $value ) {
				continue;
			}
			$results[] = sprintf( '<strong>%s:</strong> %s', $label, $value );
		}

		if ( empty( $results ) ) {
			return $this->get_empty_char();
		}

		return implode( '<br>', $results );
	}

}