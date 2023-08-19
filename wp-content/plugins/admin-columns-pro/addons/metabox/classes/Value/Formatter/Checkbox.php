<?php

namespace ACA\MetaBox\Value\Formatter;

use ACA\MetaBox\Value\Formatter;

class Checkbox implements Formatter {

	public function format( $value, $id = null ) {
		return $value
			? ac_helper()->icon->yes()
			: ac_helper()->icon->no();
	}

}