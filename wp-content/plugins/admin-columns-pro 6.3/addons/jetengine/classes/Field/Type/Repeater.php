<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\FieldFactory;

class Repeater extends Field {

	const TYPE = 'repeater';

	/**
	 * @return Field[];
	 */
	public function get_repeated_fields() {
		$field_factory = new FieldFactory();
		$settings = [];

		foreach ( $this->settings['repeater-fields'] as $field_settings ) {
			$settings[] = $field_factory->create( $field_settings );
		}

		return array_filter( $settings );

	}

}