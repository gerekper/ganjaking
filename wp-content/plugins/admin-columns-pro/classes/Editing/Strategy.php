<?php

namespace ACP\Editing;

use ACP;

interface Strategy {

	/**
	 * @param int|object $object_id
	 *
	 * @return bool True when user can edit object.
	 * @since 4.0
	 */
	public function user_has_write_permission( $object_id );

}