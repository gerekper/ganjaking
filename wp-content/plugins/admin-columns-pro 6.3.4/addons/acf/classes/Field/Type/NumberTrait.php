<?php

namespace ACA\ACF\Field\Type;

trait NumberTrait {

	public function get_min() {
		return isset( $this->settings['min'] ) && is_numeric( $this->settings['min'] )
			? (int) $this->settings['min']
			: null;
	}

	public function get_max() {
		return isset( $this->settings['max'] ) && is_numeric( $this->settings['max'] )
			? (int) $this->settings['max']
			: null;
	}

	public function get_step() {
		return isset( $this->settings['step'] ) && $this->settings['step']
			? (string) $this->settings['step']
			: 'any';
	}

}