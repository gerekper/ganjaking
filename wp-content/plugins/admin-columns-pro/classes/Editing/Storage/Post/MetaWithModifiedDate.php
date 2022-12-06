<?php

namespace ACP\Editing\Storage\Post;

class MetaWithModifiedDate extends Meta {

	public function update( int $id, $data ): bool {
		wp_update_post( [ 'ID' => $id ] );

		return parent::update( $id, $data );
	}

}