<?php

namespace ACA\Types\Editing\Storage;

class RepeatableFile extends Repeater {

	public function get( int $id ) {
		$value = array_filter( array_map( 'attachment_url_to_postid', parent::get( $id ) ) );

		return empty( $value ) ? false : $value;
	}

	public function update( int $id, $data ): bool {
		$data = is_array( $data ) ? array_map( 'wp_get_attachment_url', $data ) : false;

		return parent::update( $id, $data );
	}

}