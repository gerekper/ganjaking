<?php

namespace ACP\Export\Model\Term;

use ACP\Export\Model;

/**
 * Name (default column) exportability model
 * @since 4.1
 */
class Name extends Model {

	public function get_value( $id ) {
		return get_term( $id )->name;
	}

}