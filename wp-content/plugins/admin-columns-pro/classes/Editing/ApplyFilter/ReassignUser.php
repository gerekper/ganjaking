<?php

namespace ACP\Editing\ApplyFilter;

use AC\ApplyFilter;

class ReassignUser implements ApplyFilter {

	/**
	 * Reassign posts and links to new User ID.
	 *
	 * @param int|null $value
	 *
	 * @return int|null
	 */
	public function apply_filters( $value ): ?int {
		$value = apply_filters( 'acp/delete/reassign_user', $value );

		return $value && is_numeric( $value )
			? (int) $value
			: null;
	}

}