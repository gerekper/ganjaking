<?php

namespace ACA\ACF\Field\Type;

trait DefaultValueTrait {

	public function get_default_value() {
		return isset( $this->settings['default_value'] )
			? (string) $this->settings['default_value']
			: '';
	}

}