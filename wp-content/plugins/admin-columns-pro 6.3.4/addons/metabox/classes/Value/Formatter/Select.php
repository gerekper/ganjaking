<?php

namespace ACA\MetaBox\Value\Formatter;

use ACA\MetaBox\Value\Formatter;

class Select implements Formatter {

	/**
	 * @var array
	 */
	private $field;

	public function __construct( array $field ) {
		$this->field = $field;
	}

	public function format( $value, $id = null ) {
		if ( is_array( $value ) ) {
			return array_map( [ $this, 'get_label_for_option' ], $value );
		}

		return $this->get_label_for_option( $value );
	}

	protected function get_label_for_option( $key ) {
		$options = $this->field['options'] ?? [];

		return isset( $options[ $key ] ) && is_scalar( $options[ $key ] )
			? $options[ $key ]
			: $key;
	}

}