<?php

namespace ACP\Editing\Model\Media;

use ACP\Editing\Model;

class Title extends Model\Post {

	public function get_edit_value( $id ) {
		$post = get_post( $id );

		return $post ? $post->post_title : false;
	}

	public function get_view_settings() {
		return [
			'type'         => 'text',
			'js'           => [
				'selector' => 'strong > a',
			],
			'display_ajax' => false,
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_title' => $value ] );
	}

}