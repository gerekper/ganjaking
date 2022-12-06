<?php

namespace ACA\JetEngine\Field;

use ACA\JetEngine\Mapping;

trait OptionsTrait {

	public function get_options() {
		if ( $this instanceof GlossaryOptions && $this->has_glossary_options() ) {
			return $this->get_glossary_options();
		}

		return isset( $this->settings['options'] ) && is_array( $this->settings['options'] )
			? Mapping\Options::from_field_options( $this->settings['options'] )
			: [];
	}

}