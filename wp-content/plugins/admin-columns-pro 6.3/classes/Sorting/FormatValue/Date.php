<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Date implements FormatValue {

	public function format_value( $value ) {
		return ac_helper()->date->strtotime( maybe_unserialize( $value ) );
	}

}
