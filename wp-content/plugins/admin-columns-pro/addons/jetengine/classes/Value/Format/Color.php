<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Value\Formatter;

class Color extends Formatter {

	public function format( $raw_value ): ?string {
		return $raw_value ? ac_helper()->string->get_color_block( $raw_value ) : null;
	}

}