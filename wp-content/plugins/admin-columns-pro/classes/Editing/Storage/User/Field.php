<?php

namespace ACP\Editing\Storage\User;

use ACP\Editing\Storage;
use RuntimeException;

class Field implements Storage {

	const FIELD_REGISTERED = 'user_registered';
	const FIELD_EMAIL = 'user_email';
	const FIELD_NICENAME = 'user_nicename';
	const FIELD_URL = 'user_url';

	/**
	 * @var string
	 */
	private $field;

	public function __construct( $field ) {
		$this->field = (string) $field;
	}

	public function get( $id ) {
		return ac_helper()->user->get_user_field( $this->field, $id );
	}

	public function update( $id, $value ) {
		$args = [
			$this->field => $value,
			'ID'         => $id,
		];

		$result = wp_update_user( $args );

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		clean_user_cache( $id );

		return is_int( $result ) && $result > 0;
	}

}