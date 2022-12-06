<?php

namespace ACA\ACF\Field\Type;

trait ValueDecoratorTrait {

	public function get_append() {
		return (string) $this->settings['append'];
	}

	public function get_prepend() {
		return (string) $this->settings['prepend'];
	}

}