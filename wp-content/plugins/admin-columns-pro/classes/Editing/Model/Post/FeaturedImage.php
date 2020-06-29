<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class FeaturedImage extends Model\Post {

	public function get_view_settings() {
		return [
			'type'         => 'media',
			'attachment'   => [
				'library' => [
					'type' => 'image',
				],
			],
			'clear_button' => true,
		];
	}

	public function save( $id, $value ) {
		$this->update_post( $id );

		if ( $this->has_error() ) {
			return false;
		}

		return $value
			? (bool) set_post_thumbnail( $id, $value )
			: delete_post_thumbnail( $id );
	}

}