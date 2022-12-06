<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Column;
use ACA\Types\Export;

/**
 * @property Column $column
 */
class Skype extends Export\Field {

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$values = [];

		foreach ( (array) $this->column->get_raw_value( $id ) as $value ) {
			if ( ! empty( $value['skypename'] ) ) {
				$values[] = $value['skypename'];
			}
		}

		return implode( ', ', array_filter( $values ) );
	}

}