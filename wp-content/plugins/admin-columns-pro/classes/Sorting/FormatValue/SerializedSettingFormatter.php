<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class SerializedSettingFormatter implements FormatValue {

	/**
	 * @var FormatValue
	 */
	private $formatter;

	/**
	 * @param FormatValue $formatter
	 */
	public function __construct( FormatValue $formatter ) {
		$this->formatter = $formatter;
	}

	public function format_value( $string ) {
		$values = maybe_unserialize( $string );

		if ( empty( $values ) || ! is_array( $values ) ) {
			return null;
		}

		$formatted = array_map( [ $this->formatter, 'format_value' ], $values );

		return implode( ' ', $formatted );
	}

}