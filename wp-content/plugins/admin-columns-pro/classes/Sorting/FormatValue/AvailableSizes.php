<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class AvailableSizes implements FormatValue {

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data['sizes'] )
			? count( $data['sizes'] )
			: null;
	}

}
