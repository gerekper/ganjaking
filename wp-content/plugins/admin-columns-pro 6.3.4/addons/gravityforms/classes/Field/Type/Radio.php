<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;

class Radio extends GravityForms\Field\Field implements GravityForms\Field\Options {

	public function get_options() {
		return GravityForms\Utils\FormField::formatChoices( $this->gf_field->choices );
	}

}