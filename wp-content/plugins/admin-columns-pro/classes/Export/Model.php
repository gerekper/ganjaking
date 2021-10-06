<?php

namespace ACP\Export;

use ACP;

/**
 * Exportability model, which can be attached as an extension to a column. It handles custom
 * behaviour a column should exhibit when being exported
 */
abstract class Model extends ACP\Model {

	/**
	 * Retrieve the value to be exported by the column for a specific item
	 *
	 * @param int $id Item ID
	 *
	 * @return string
	 */
	abstract public function get_value( $id );

	/**
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

}