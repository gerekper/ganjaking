<?php

namespace ACA\JetEngine\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Media implements FormatValue {

	public function format_value( $value ) {
		return basename( get_attached_file( $value ) );
	}

}