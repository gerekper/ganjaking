<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Column;
use ACA\Types\Export;

class Date extends Export\Field {

	/**
	 * @var string
	 */
	private $date_format;

	public function __construct( Column $column, $date_format = 'Y-m-d' ) {
		parent::__construct( $column );

		$this->date_format = $date_format;
	}

	public function get_value( $id ) {
		$values = (array) $this->column->get_raw_value( $id );
		$dates = [];

		foreach ( $values as $value ) {
			if ( empty( $value ) || ! is_numeric( $value ) ) {
				continue;
			}

			$dates[] = date( $this->date_format, $value );
		}

		return implode( ', ', $dates );
	}

}