<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\Strategy;
use WP_User;

class User implements Strategy {

	/**
	 * @param $user
	 *
	 * @return bool
	 */
	public function user_has_write_permission( $user ) {
		if ( ! $user instanceof WP_User ) {
			$user = get_userdata( $user );

			if ( ! $user instanceof WP_User ) {
				return false;
			}
		}

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return false;
		}

		return true;
	}

}