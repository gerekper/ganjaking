<?php

namespace ACA\Types\Editing\Storage;

use ACP;

class File extends ACP\Editing\Storage\Meta {

	public function get( int $id ) {
		$raw = parent::get( $id );

		return $raw ? attachment_url_to_postid( $raw ) : false;
	}

	public function update( int $id, $data ): bool {
		$_value = is_numeric( $data ) ? wp_get_attachment_url( $data ) : $data;

		return parent::update( $id, $_value );
	}

}