<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Height implements FormatValue {

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data['height'] )
			? $data['height']
			: null;
	}

}
