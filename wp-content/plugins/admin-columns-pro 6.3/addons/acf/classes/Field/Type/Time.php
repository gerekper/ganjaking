<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Time extends Field {

	public function get_display_format() {
		return (string) $this->settings['display_format'];
	}

}