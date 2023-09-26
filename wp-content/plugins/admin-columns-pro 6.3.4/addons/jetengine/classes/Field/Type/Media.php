<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\ValueFormat;

class Media extends Field implements ValueFormat {

	const TYPE = 'media';

	public function get_value_format() {
		return isset( $this->settings['value_format'] ) ? $this->settings['value_format'] : ValueFormat::FORMAT_ID;
	}

}