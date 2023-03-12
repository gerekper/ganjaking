<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Export;
use ACA\Types\Field;

class Checkboxes extends Export\Field {

	public function get_value( $id ) {
		$field = $this->column->get_field();

		if ( ! $field instanceof Field\Checkboxes ) {
			return false;
		}

		return implode( ', ', array_filter( $field->get_values_as_labels( $id ) ) );
	}

}