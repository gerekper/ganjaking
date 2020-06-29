<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Exif implements FormatValue {

	/**
	 * @var string
	 */
	private $field;

	public function __construct( $field ) {
		$this->field = $field;
	}

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data[ $this->field ] )
			? $data[ $this->field ]
			: null;
	}

}
