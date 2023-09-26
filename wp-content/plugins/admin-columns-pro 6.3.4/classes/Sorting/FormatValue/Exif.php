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

		if ( ! isset( $data['image_meta'], $data['image_meta'][ $this->field ] ) ) {
			return null;
		}

		return $data['image_meta'][ $this->field ];
	}

}
