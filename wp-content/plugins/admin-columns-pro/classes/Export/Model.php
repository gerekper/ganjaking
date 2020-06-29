<?php

namespace ACP\Export;

use ACP;

/**
 * Exportability model, which can be attached as an extension to a column. It handles custom
 * behaviour a column should exhibit when being exported
 * @since 1.0
 */
abstract class Model extends ACP\Model {

	/**
	 * Retrieve the value to be exported by the column for a specific item
	 *
	 * @param int $id Item ID
	 *
	 * @since 1.0
	 */
	abstract public function get_value( $id );

	/**
	 * @since 1.0
	 */
	public function is_active() {
		return true;
	}

}