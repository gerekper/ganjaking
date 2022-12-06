<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms\Field\Field;

class Consent extends Field {

	public function get_consent_text() {
		return $this->gf_field->offsetGet( 'checkboxLabel' );
	}

}