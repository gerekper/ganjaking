<?php

namespace ACP\Helper\Select\Group;

use AC;
use WP_Post;

class MimeType extends AC\Helper\Select\Group {

	/**
	 * @param WP_Post                 $post
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $post, AC\Helper\Select\Option $option ) {
		$extension = ac_helper()->image->get_file_extension( $post->ID );
		$label = $extension;

		foreach ( wp_get_ext_types() as $type => $extensions ) {
			if ( in_array( $extension, $extensions ) ) {
				$label = $this->get_translated_label( $type );
			}
		}

		return $label;
	}

	private function get_translated_label( $type ) {
		$translations = [
			'image'       => _x( 'Image', 'mediatype', 'codepress-admin-columns' ),
			'audio'       => _x( 'Audio', 'mediatype', 'codepress-admin-columns' ),
			'video'       => _x( 'Video', 'mediatype', 'codepress-admin-columns' ),
			'document'    => _x( 'Document', 'mediatype', 'codepress-admin-columns' ),
			'spreadsheet' => _x( 'Spreadsheet', 'mediatype', 'codepress-admin-columns' ),
			'interactive' => _x( 'Interactive', 'mediatype', 'codepress-admin-columns' ),
			'text'        => _x( 'Text', 'mediatype', 'codepress-admin-columns' ),
			'archive'     => _x( 'Archive', 'mediatype', 'codepress-admin-columns' ),
			'code'        => _x( 'Code', 'mediatype', 'codepress-admin-columns' ),
		];

		if ( isset( $translations[ $type ] ) ) {
			$type = $translations[ $type ];
		}

		return $type;
	}

}