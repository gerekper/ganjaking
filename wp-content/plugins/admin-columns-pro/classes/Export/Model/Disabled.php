<?php

namespace ACP\Export\Model;

use ACP\Export\Model;

/**
 * Exportability model for outputting the column's output value
 * @since 4.1
 */
class Disabled extends Model {

	public function is_active() {
		return false;
	}

	public function get_value( $id ) {
		return false;
	}

}