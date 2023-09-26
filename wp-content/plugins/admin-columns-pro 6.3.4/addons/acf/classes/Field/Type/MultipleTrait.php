<?php

namespace ACA\ACF\Field\Type;

trait MultipleTrait {

	public function is_multiple() {
		return isset( $this->settings['multiple'] ) && $this->settings['multiple'];
	}

}