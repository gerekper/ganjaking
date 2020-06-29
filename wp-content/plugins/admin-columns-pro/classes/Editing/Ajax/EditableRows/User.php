<?php

namespace ACP\Editing\Ajax\EditableRows;

use ACP\Editing\Ajax\EditableRows;
use WP_User_Query;

final class User extends EditableRows {

	public function register() {
		add_action( 'users_list_table_query_args', [ $this, 'send_editable_rows' ], PHP_INT_MAX - 100 );
	}

	public function send_editable_rows( array $args ) {
		$this->check_nonce();

		$query = new WP_User_Query( array_merge( $args, [
			'fields' => 'all',
			'number' => $this->get_editable_rows_per_iteration(),
			'offset' => $this->get_offset(),
		] ) );

		$editable_rows = [];

		foreach ( $query->get_results() as $user ) {
			if ( $this->strategy->user_has_write_permission( $user ) ) {
				$editable_rows[] = $user->ID;
			}
		}

		return $this->success( $editable_rows );
	}

}