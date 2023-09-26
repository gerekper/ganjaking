<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms\Field\Field;

class Input extends Field {

	/**
	 * @return string
	 */
	public function get_input_type() {
		switch ( $this->gf_field->offsetGet( 'type' ) ) {
			case 'website':
				return 'url';
			default:
				return 'text';
		}
	}

}