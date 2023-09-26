<?php

namespace ACP\Editing\Storage\User;

use ACP\Editing\Storage;

class FullName implements Storage {

	const KEY_FIRST_NAME = 'first_name';
	const KEY_LAST_NAME = 'last_name';

	public function get( int $id ) {
		return [
			'first_name' => get_user_meta( $id, self::KEY_FIRST_NAME, true ),
			'last_name'  => get_user_meta( $id, self::KEY_LAST_NAME, true ),
		];
	}

	public function update( int $id, $data ): bool {
		if ( isset( $data['first_name'] ) ) {
			update_user_meta( $id, self::KEY_FIRST_NAME, $data['first_name'] );
		}
		if ( isset( $data['last_name'] ) ) {
			update_user_meta( $id, self::KEY_LAST_NAME, $data['last_name'] );
		}

		return true;
	}

}