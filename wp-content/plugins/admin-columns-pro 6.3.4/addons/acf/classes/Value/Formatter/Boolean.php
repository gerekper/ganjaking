<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class Boolean extends Formatter {

	public function format( $value, $id = null ) {
		return ac_helper()->icon->yes_or_no( $value );
	}

}