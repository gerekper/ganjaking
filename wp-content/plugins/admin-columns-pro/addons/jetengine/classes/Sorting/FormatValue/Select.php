<?php

namespace ACA\JetEngine\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Select implements FormatValue {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( array $options ) {
		$this->options = $options;
	}

	public function format_value( $value ) {
		$values = maybe_unserialize( $value );

		if ( ! $values || ! is_array( $values ) ) {
			return null;
		}

		$formatted = [];

		foreach ( $values as $option_name ) {
			if ( isset( $this->options[ $option_name ] ) ) {
				$formatted[] = $this->options[ $option_name ];
			}
		}

		return implode( ' ', $formatted );
	}

}