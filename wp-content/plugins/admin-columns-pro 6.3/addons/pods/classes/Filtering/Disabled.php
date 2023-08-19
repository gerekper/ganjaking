<?php

namespace ACA\Pods\Filtering;

use ACP;

class Disabled extends ACP\Filtering\Model {

	public function is_active() {
		return false;
	}

	public function register_settings() {
	}

	public function get_filtering_vars( $vars ) {
		return $vars;
	}

	public function get_filtering_data() {
		return [];
	}

}