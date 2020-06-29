<?php

namespace ACP\Export\Model\Term;

use ACP\Export\Model;

/**
 * Name (default column) exportability model
 * @since 4.1
 */
class Description extends Model {

	public function get_value( $id ) {
		return get_term( $id )->description;
	}

}