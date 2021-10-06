<?php

namespace ACP\Editing\Storage\Post;

class MetaWithModifiedDate extends Meta {

	public function update( $id, $value ) {
		wp_update_post( [ 'ID' => $id ] );

		return parent::update( $id, $value );
	}

}