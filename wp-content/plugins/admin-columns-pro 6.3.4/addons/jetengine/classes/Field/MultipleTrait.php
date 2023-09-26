<?php

namespace ACA\JetEngine\Field;

trait MultipleTrait {

	public function is_multiple() {
		return isset( $this->settings['is_multiple'] ) && $this->settings['is_multiple'];
	}

}