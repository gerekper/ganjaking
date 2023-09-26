<?php

namespace ACP\Filtering\Model;

use ACP\Filtering\Model;

class Disabled extends Model {

	public function is_active() {
		return false;
	}

	public function get_filtering_vars( $vars ) {
		return $vars;
	}

	public function get_filtering_data() {
		return [];
	}

	public function register_settings() {
	}

}