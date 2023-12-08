<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\OptionsFactory;

use AC\Helper\Select\Options;

class MimeType {

	public function create( string $post_type ): Options {
		$mime_types = $this->get_mimetypes( $post_type );

		return Options::create_from_array( array_combine( $mime_types, $mime_types ) );
	}

	private function get_mimetypes( string $post_type ): array {
		global $wpdb;

		$sql = "
			SELECT DISTINCT post_mime_type
			FROM $wpdb->posts
			WHERE post_type = %s
			AND post_mime_type <> ''
			ORDER BY 1
		";

		$values = $wpdb->get_col( $wpdb->prepare( $sql, $post_type ) );

		if ( empty( $values ) ) {
			return [];
		}

		return $values;
	}

}