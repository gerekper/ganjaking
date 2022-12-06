<?php

namespace ACA\GravityForms\Column\Entry;

use ACA\GravityForms\Column;
use GFAPI;

class Address extends Column\Entry {

	public function get_value( $id ) {
		return GFAPI::get_field( $this->get_form_id(), $this->get_field_id() )->get_value_entry_detail( GFAPI::get_entry( $id ) );
	}

}