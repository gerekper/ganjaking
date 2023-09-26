<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class StripContent implements FormatValue {

	public function format_value( $string ) {
		return trim( strip_shortcodes( strip_tags( $string ) ) );
	}

}
