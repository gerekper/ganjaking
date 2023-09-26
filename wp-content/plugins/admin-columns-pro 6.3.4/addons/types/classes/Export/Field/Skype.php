<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Export;

class Skype extends Export\Field {

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