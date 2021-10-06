<?php

namespace ACP\Editing\Service\User;

use AC\Request;
use ACP\Editing;
use ACP\Editing\View;

class FullName implements Editing\Service {

	const KEY_FIRST_NAME = 'first_name';
	const KEY_LAST_NAME = 'last_name';

	public function get_value( $id ) {
		return [
			'first_name' => get_user_meta( $id, self::KEY_FIRST_NAME, true ),
			'last_name'  => get_user_meta( $id, self::KEY_LAST_NAME, true ),
		];
	}

	public function get_view( $context ) {
		return new View\FullName();
	}

	public function update( Request $request ) {
		$value = $request->get( 'value' );
		$id = (int) $request->get( 'id' );

		if ( isset( $value['first_name'] ) ) {
			update_user_meta( $id, self::KEY_FIRST_NAME, $value['first_name'] );
		}

		if ( isset( $value['last_name'] ) ) {
			update_user_meta( $id, self::KEY_LAST_NAME, $value['last_name'] );
		}
	}

}