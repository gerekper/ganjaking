<?php

namespace ACP\Export\Model;

use ACP\Export\Model;

/**
 * @since      4.1
 * @deprecated 6.0
 */
class Disabled extends Model {

	public function is_active() {
		return false;
	}

	public function get_value( $id ) {
		return false;
	}

}