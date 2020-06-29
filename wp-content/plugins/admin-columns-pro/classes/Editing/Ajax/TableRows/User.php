<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class User extends TableRows {

	public function register() {
		add_action( 'users_list_table_query_args', [ $this, 'handle_request' ] );
	}

}