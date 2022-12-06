<?php

namespace ACA\MetaBox\Export\Model;

use ACA\MetaBox\Column;

/**
 * @property Column $column
 */
class FieldsetText extends Raw {

	public function format_single_value( $value, $id = null ) {
		$value = $this->column->format_single_value( $value, $id );
		$value = str_replace( '<br>', "\r\n", $value );

		return strip_tags( $value );
	}

}