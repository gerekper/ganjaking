<?php

namespace ACA\GravityForms\Column\Entry;

use ACA\GravityForms\Column;
use GFAPI;
use GFFormsModel;

class ProductSelect extends Column\Entry {

	public function get_value( $id ) {
		$field = GFFormsModel::get_field( $this->get_form_id(), $this->get_field_id() );

		return $field ? $field->get_value_export( GFAPI::get_entry( $id ) ) : null;
	}

}