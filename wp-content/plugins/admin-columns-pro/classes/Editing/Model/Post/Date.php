<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Date extends Model\Post {

	public function get_edit_value( $id ) {
		$post = get_post( $id );

		if ( ! $post ) {
			return null;
		}

		if ( in_array( $post->post_status, [ 'draft', 'inherit' ] ) ) {
			return null;
		}

		return $post->post_date;
	}

	public function get_view_settings() {
		return [
			'type' => 'date_time',
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [
			'post_date'     => $value,
			'post_date_gmt' => get_gmt_from_date( $value ),
		] );
	}

}