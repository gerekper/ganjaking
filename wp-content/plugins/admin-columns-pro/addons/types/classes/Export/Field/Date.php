<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Export;

class Date extends Export\Field {

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$values = (array) $this->column->get_raw_value( $id );
		$dates = [];

		foreach ( $values as $value ) {
			if ( empty( $value ) || ! is_numeric( $value ) ) {
				continue;
			}

			$dates[] = date( 'Y-m-d', $value );
		}

		return implode( ', ', $dates );
	}

}