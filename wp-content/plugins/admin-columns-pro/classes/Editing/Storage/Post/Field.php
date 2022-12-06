<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;
use RuntimeException;

class Field implements Storage {

	/**
	 * @var string
	 */
	private $field;

	public function __construct( $field ) {
		$this->field = (string) $field;
	}

	public function get( int $id ) {
		return get_post_field( $this->field, $id, 'raw' );
	}

	public function update( int $id, $data ): bool {
		$args = [
			'ID'         => $id,
			$this->field => $data,
		];

		$result = wp_update_post( $args );

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return is_int( $result ) && $result > 0;
	}

}