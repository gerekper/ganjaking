<?php

namespace ACA\MetaBox\Column;

class Color extends Text {

	public function format_single_value( $value, $id = null ) {
		return ac_helper()->string->get_color_block( $value );
	}

}