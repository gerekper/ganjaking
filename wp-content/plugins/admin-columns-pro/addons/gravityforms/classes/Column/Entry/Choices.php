<?php

namespace ACA\GravityForms\Column\Entry;

use ACA\GravityForms\Column;
use ACA\GravityForms\Field\Options;
use ACA\GravityForms\Settings\ChoiceDisplay;

class Choices extends Column\Entry {

	public function register_settings() {
		$field = $this->get_field();

		$this->add_setting( new ChoiceDisplay( $this, $field instanceof Options ? $field->get_options() : [] ) );
	}

}