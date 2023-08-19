<?php

namespace ACA\ACF\Field\Type;

trait ChoicesTrait {

	public function get_choices() {
		return isset( $this->settings['choices'] ) && $this->settings['choices']
			? (array) $this->settings['choices']
			: [];
	}

}