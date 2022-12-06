<?php

namespace ACA\ACF\Field\Type;

trait MaxLengthTrait {

	/**
	 * @return int|null
	 */
	public function get_maxlength() {
		return isset( $this->settings['maxlength'] ) && is_numeric( $this->settings['maxlength'] )
			? (int) $this->settings['maxlength']
			: null;
	}

}