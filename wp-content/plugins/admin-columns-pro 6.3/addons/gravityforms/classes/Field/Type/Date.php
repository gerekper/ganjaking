<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;

class Date extends GravityForms\Field\Field {

	public function get_stored_date_format() {
		return 'Y-m-d';
	}
}