<?php

namespace ACP\Editing\Model;

use ACP\Editing\Model;

class Disabled extends Model {

	public function register_settings() {
		// Settings Disabled. Leave empty.
	}

	protected function save( $id, $value ) {
		return true;
	}

}