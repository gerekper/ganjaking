<?php

namespace ACP\Helper\Select\Value;

use AC;
use WP_User;

final class User
	implements AC\Helper\Select\Value {

	/**
	 * @param WP_User $user
	 *
	 * @return int
	 */
	public function get_value( $user ) {
		return $user->ID;
	}
}