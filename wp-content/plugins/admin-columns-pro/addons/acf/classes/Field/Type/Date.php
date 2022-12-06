<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Date extends Field
	implements Field\Date, Field\SaveFormat {

	public function get_display_format() {
		return (string) $this->settings['display_format'];
	}

	public function get_first_day() {
		return (int) $this->settings['first_day'];
	}

	public function get_save_format() {
		return isset( $this->settings['save_format'] )
			? ac_helper()->date->parse_jquery_dateformat( $this->settings['save_format'] )
			: 'Ymd';
	}

}