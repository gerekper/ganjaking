<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Slug extends Model\Post {

	public function get_edit_value( $id ) {
		return get_post_field( 'post_name', $id, 'raw' );
	}

	public function get_view_settings() {
		return [
			'type'                   => 'text',
			'placeholder'            => __( 'Enter slug', 'codepress-admin-columns' ),
			self::VIEW_BULK_EDITABLE => false,
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_name' => $value ] );
	}

}