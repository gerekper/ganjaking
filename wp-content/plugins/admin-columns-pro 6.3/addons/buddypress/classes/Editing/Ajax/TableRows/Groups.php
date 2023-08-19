<?php

namespace ACA\BP\Editing\Ajax\TableRows;

use ACP;

class Groups extends ACP\Editing\Ajax\TableRows {

	public function register(): void
    {
		add_action( 'bp_groups_admin_load', [ $this, 'handle_request' ] );
	}

}