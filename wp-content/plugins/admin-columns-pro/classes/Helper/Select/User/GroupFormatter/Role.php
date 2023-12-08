<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\User\GroupFormatter;

use ACP\Helper\Select\User\GroupFormatter;
use WP_User;

class Role implements GroupFormatter {

	public function format( WP_User $user ): string {

		$role = ac_helper()->user->get_role_name( $user->roles[0] );

		return $role ?: __( 'No Roles', 'codepress-admin-columns' );
	}

}