<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Value\Formatter;

class Switcher extends Formatter {

	public function format( $raw_value ): ?string {
		return ac_helper()->icon->yes_or_no( $raw_value === 'true' );
	}

}