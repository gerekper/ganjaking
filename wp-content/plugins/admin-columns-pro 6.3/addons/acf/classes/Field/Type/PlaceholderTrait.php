<?php

namespace ACA\ACF\Field\Type;

trait PlaceholderTrait {

	public function get_placeholder() {
		return (string) $this->settings['placeholder'];
	}

}