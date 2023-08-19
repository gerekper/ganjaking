<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms\Field\Field;

class Textarea extends Field {

	/**
	 * @return string
	 */
	public function get_input_type() {
		return 'textarea';
	}

}