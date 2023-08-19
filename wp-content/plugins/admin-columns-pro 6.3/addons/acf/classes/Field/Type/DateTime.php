<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class DateTime extends Field
	implements Field\Date, Field\SaveFormat {

	public function get_display_format() {
		return (string) $this->settings['display_format'];
	}

	public function get_first_day() {
		return (int) $this->settings['first_day'];
	}

	public function get_save_format() {
		return 'Y-m-d H:i:s';
	}

}