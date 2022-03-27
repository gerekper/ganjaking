<?php

namespace ACP\Access\Rule;

use ACP;
use ACP\Access\Permissions;
use ACP\Access\Platform;

class LocalServer implements ACP\Access\Rule {

	public function get_permissions() {
		$permissions = new Permissions();

		if ( Platform::is_local() ) {
			$permissions = $permissions->with_permission( Permissions::USAGE );
		}

		return $permissions;
	}

}