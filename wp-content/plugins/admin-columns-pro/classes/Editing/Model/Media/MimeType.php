<?php

namespace ACP\Editing\Model\Media;

use ACP\Editing\Model;

class MimeType extends Model\Post {

	public function get_view_settings() {
		$mime_types = wp_get_mime_types();
		$options = array_combine( $mime_types, $mime_types );

		return [
			'type'    => 'select',
			'options' => $options,
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_mime_type' => $value ] );
	}

}