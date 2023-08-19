<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\ValueFormat;

class Gallery extends Field implements ValueFormat {

	const TYPE = 'gallery';

	public function get_value_format() {
		return isset( $this->settings['value_format'] ) ? $this->settings['value_format'] : '';
	}

}