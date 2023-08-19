<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class CloneField extends Field {

	public function is_prefixed() {
		$name = (int) $this->settings['prefix_name'];

		return 1 === $name;
	}

}