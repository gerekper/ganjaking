<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class Color extends Formatter {

	public function format( $value, $id = null ) {
		if ( ! $value ) {
			return $this->column->get_empty_char();
		}

		return ac_helper()->string->get_color_block( $value );
	}

}