<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class FileName implements FormatValue {

	public function format_value( $file ) {
		return strtolower( basename( $file ) );
	}

}
