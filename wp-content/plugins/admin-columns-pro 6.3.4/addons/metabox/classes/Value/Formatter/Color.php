<?php

namespace ACA\MetaBox\Value\Formatter;

use ACA\MetaBox\Value\Formatter;

class Color implements Formatter {

	public function format( $value, $id = null ) {
		return ac_helper()->string->get_color_block( $value );
	}

}