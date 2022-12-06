<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;
use InvalidArgumentException;

class Date implements Storage {

	public function get( int $id ) {
		return get_post_field( 'post_date', $id, 'raw' );
	}

	public function update( int $id, $data ): bool {
		if ( ! $data || ! is_string( $data ) ) {
			throw new InvalidArgumentException( 'Date must be a string.' );
		}

		$args = [
			'ID'            => $id,
			'post_date'     => $data,
			'post_date_gmt' => get_gmt_from_date( $data ),
		];

		return is_numeric( wp_update_post( $args ) );
	}

}