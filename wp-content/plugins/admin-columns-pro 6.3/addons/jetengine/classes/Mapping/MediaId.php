<?php

namespace ACA\JetEngine\Mapping;

final class MediaId {

	public static function to_array( $id ) {
		return [
			'url' => wp_get_attachment_url( $id ),
			'id'  => $id,
		];
	}

	public static function from_array( $entry ) {
		return is_array( $entry ) && isset( $entry['id'] )
			? $entry['id']
			: null;
	}

	public static function from_url( $url ) {
		return $url
			? attachment_url_to_postid( $url )
			: null;
	}

	public static function to_url( $id ) {
		return wp_get_attachment_url( $id );
	}

}