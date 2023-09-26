<?php

namespace ACA\JetEngine\Field;

trait DefaultValueTrait {

	/**
	 * @return bool
	 */
	public function has_default_value() {
		return isset( $this->settings['default_val'] ) && $this->settings['default_val'] != '';
	}

	/**
	 * @return string|null
	 */
	public function get_default_value() {
		return $this->has_default_value()
			? $this->settings['default_val']
			: null;
	}

}