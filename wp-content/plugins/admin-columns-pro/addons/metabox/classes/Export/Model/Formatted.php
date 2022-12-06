<?php

namespace ACA\MetaBox\Export\Model;

use ACA\MetaBox\Column;

/**
 * @property Column $column
 */
class Formatted extends Raw {

	public function format_single_value( $value, $id = null ) {
		$value = strip_tags( $this->column->format_single_value( $value, $id ) );

		return str_replace( $this->column->get_empty_char(), '', $value );
	}

}