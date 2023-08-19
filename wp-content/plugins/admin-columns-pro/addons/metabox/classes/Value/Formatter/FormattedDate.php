<?php

namespace ACA\MetaBox\Value\Formatter;

use ACA\MetaBox\Value\Formatter;

class FormattedDate implements Formatter {

	public function format( $value, $id = null ) {
		return is_array( $value ) && isset( $value['formatted'] )
			? $value['formatted']
			: $value;
	}

}