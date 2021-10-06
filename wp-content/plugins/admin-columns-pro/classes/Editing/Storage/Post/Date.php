<?php

namespace ACP\Editing\Storage\Post;

use ACP\Editing\Storage;
use InvalidArgumentException;

class Date implements Storage {

	public function get( $id ) {
		return get_post_field( 'post_date', $id, 'raw' );
	}

	public function update( $id, $date ) {
		if ( ! $date || ! is_string( $date ) ) {
			throw new InvalidArgumentException( 'Date must be a string.' );
		}

		$args = [
			'ID'            => $id,
			'post_date'     => $date,
			'post_date_gmt' => get_gmt_from_date( $date ),
		];

		return is_numeric( wp_update_post( $args ) );
	}

}