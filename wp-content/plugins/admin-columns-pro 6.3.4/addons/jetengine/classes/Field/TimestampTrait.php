<?php

namespace ACA\JetEngine\Field;

trait TimestampTrait {

	public function is_timestamp() {
		return isset( $this->settings['is_timestamp'] ) && $this->settings['is_timestamp'];
	}

}