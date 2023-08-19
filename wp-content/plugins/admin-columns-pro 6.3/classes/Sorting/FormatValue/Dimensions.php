<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Dimensions implements FormatValue {

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data['width'], $data['height'] )
			? $data['width'] * $data['height']
			: null;
	}

}
