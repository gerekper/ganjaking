<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class SerializedKey implements FormatValue {

	/**
	 * @var string
	 */
	private $key;

	public function __construct( $key ) {
		$this->key = $key;
	}

	public function format_value( $value ) {
		$data = maybe_unserialize( $value );

		return isset( $data[ $this->key ] )
			? $data[ $this->key ]
			: null;
	}

}
