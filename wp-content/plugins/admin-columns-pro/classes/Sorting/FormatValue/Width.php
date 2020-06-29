<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Width implements FormatValue {

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data['width'] )
			? $data['width']
			: null;
	}

}
